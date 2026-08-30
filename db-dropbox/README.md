# 🗃️ db-dropbox — the place to drop your database changes

Drop database change files here, one file per change, named clearly.

**This is for the LIVE databases only.** For local development, just change
your own local database directly — you don't need to put anything here.

## How it works

1. You write the change as a `.sql` file and drop it in this folder.
2. Name it with the date first, e.g.:
   - `db-dropbox/db-2026-08-30_fix_leave_balance.sql`
   - `db-dropbox/db-2026-08-31_seed_new_shifts.sql`
3. Commit & push (or open a Pull Request) so it syncs with the repo.
4. The site owner (or a reviewer) approves, then runs it against the live
   database **manually** in phpMyAdmin.

## Important

- These files are **NOT run automatically.** The hosting (InfinityFree) does
  not allow the server to connect to its own database, so a human must paste
  each file into phpMyAdmin once. The folder just makes sharing the changes
  easy.
- Every file should be **safe to run once** (an `UPDATE`/`INSERT` that affects
  only the specific rows you intend). Prefer `UPDATE ... WHERE <specific id>`.
- Label destructive changes (DELETE / DROP / big overwrites) clearly in the
  file's top comment and in your commit message.
- Do NOT put passwords, real employee emails, or anything sensitive in these
  files.

## Example

```sql
-- db-2026-08-30_fix_leave_balance.sql
-- Fix: adjust remaining leave balance for employee 42.
UPDATE employee_leave_balances
   SET balance_days = 5
 WHERE employee_id = 42;
```
