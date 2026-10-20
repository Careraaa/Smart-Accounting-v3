# Database exports — one structure for every branch

## The problem this solves

The four environments (`main`, `deploy-test`, `deploy-test-2`, `deploy-sqa`) run the
**same 26 migrations**, so their databases must have the **same structure**.

They did not. Each branch carried a different dump:

| File | State |
|---|---|
| `db-dropbox/db-2026-08-31_FULL_RESET_database.sql` | **stale** — missing 10+ migrations (`boundary_payments`, `is_contra`, `weekday_boundary`/`weekend_boundary`, `employer_sss`, `shift_id`, …) |
| `database/manual/test1_READY.sql` | current — matches the code |
| `database/sqa-deploy-structure.sql` | current — matches the code |

Importing the stale one leaves columns missing and the `migrations` table empty, so
Laravel tries to re-create tables that already exist.

## The fix

Three files, all under `database/manual/`:

| File | Role |
|---|---|
| `CANONICAL_SCHEMA.sql` | **single source of truth** for structure (50 tables + all 26 migrations recorded) |
| `build-target-dump.php` | builds any target export from the canonical schema |
| `verify-target-dumps.php` | proves two exports have identical structure |

The builder **always** emits the canonical DDL — never the source dump's own DDL.
So even a stale source still produces a correct schema. That is what makes the
exports interchangeable.

## Usage

```bash
# 1. Refresh the canonical schema (only when the code's migrations change)
php database/manual/build-target-dump.php --source=database/manual/test1_READY.sql --make-schema

# 2. Build a target export
php database/manual/build-target-dump.php \
    --source=database/manual/test1_READY.sql \
    --profile=sqa \
    --out=database/manual/target-dump-sqa.sql

# 3. Verify any set of exports agree
php database/manual/verify-target-dumps.php \
    database/manual/target-dump-sqa.sql \
    database/manual/target-dump-test1.sql
```

## Profiles

| Profile | Target | Data |
|---|---|---|
| `main` | production | full, from the source dump |
| `test1` | test1 | full, from the source dump |
| `test2` | test2 | full, from the source dump |
| `sqa` | sqa | reference data + **5 staff accounts only**, no demo data |

## Importing

Each export starts with `DROP TABLE IF EXISTS` for all 50 tables, so importing over
an existing database replaces it cleanly. Import through **phpMyAdmin → Import**.

The `migrations` table is always populated with all 26 migration names, so Laravel
will not try to re-run a migration after import.

## Note on `salary_loans`

That table exists **only** in the old Aug-31 dump and has **no migration**, so it is
not in the canonical schema. The loan feature therefore cannot work in any freshly
imported environment until a migration for it is added.
