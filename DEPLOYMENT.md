# Deploying Smart Accounting to InfinityFree (Free)

Auto-deploy pipeline: push code → GitHub Actions builds it → FTP-uploads to InfinityFree.

```
main        ──►  https://YOURDOMAIN.infinityfreeapp.com        (production)
deploy-test ──►  https://test.YOURDOMAIN.infinityfreeapp.com   (sandbox)
```

---

## 1. One-time: InfinityFree setup

1. Create account at infinityfree.com
2. Note from **FTP Details** sidebar:
   | Item | Example |
   |---|---|
   | FTP user | `epiz_31234567` |
   | FTP password | `********` |
   | FTP host | `ftpupload.net` |
3. Main domain `yourname.infinityfreeapp.com` exists by default → its folder is `/htdocs/`
4. **Subdomain** panel → create `test.yourname` → gets its own folder like `/test.yourname.infinityfreeapp.com/htdocs/`
5. **MySQL Databases** panel → create TWO databases:
   - main DB (production)
   - test DB (sandbox)
   Note each one's full name / user / password / host (`sqlXXX.infinityfree.com`)
6. Import your local database:
   - Local phpMyAdmin → `smart_accounting_v3` → Export → `.sql`
   - InfinityFree phpMyAdmin (per DB) → Import
   - Do this for BOTH databases

## 2. One-time: GitHub Secrets

Repo → Settings → Secrets and variables → Actions → New repository secret:

| Secret name | Value |
|---|---|
| `FTP_USERNAME` | epiz_XXXX |
| `FTP_PASSWORD` | FTP password |
| `FTP_SERVER_DIR_PROD` | `/htdocs/` |
| `FTP_SERVER_DIR_TEST` | `/test.yourname.infinityfreeapp.com/htdocs/` *(check exact folder name in File Manager)* |
| `PROD_ENV_FILE` | production `.env` content (template below) |
| `TEST_ENV_FILE` | test `.env` content (same template, different DB) |

## 3. Production .env template

Get a fresh key first, run locally:
```bash
php artisan key:generate --show
```

Then build the secret value:
```env
APP_NAME="Smart Accounting"
APP_ENV=production
APP_KEY=paste-key-here
APP_DEBUG=false
APP_URL=https://yourname.infinityfreeapp.com

APP_LOCALE=en
APP_FALLBACK_LOCALE=en

LOG_CHANNEL=stack
LOG_STACK=single
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=sqlXXX.infinityfree.com
DB_PORT=3306
DB_DATABASE=epiz_xxxxx_main
DB_USERNAME=epiz_xxxxx
DB_PASSWORD=********

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

CACHE_STORE=database

MAIL_MAILER=log
MAIL_SCHEME=null
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="no-reply@yourname.infinityfreeapp.com"
MAIL_FROM_NAME="${APP_NAME}"

VITE_APP_NAME="${APP_NAME}"
```

Test env = same file but with the **test** DB credentials and `https://test....` URL.

## 4. Deploying

```bash
# sandbox (break stuff here freely)
git checkout deploy-test
git push origin deploy-test

# when it works -> production
git checkout main
git merge deploy-test
git push origin main
```

Watch progress: repo → **Actions** tab.
First deploy: **15–40 min** (~10k files over FTP). Later deploys only sync changed files.

## 5. After each deploy — database migrations

The pipeline uploads **code only**. It never runs `php artisan migrate`, because the
InfinityFree DB is not reachable from the GitHub runner and there is no SSH.

**If a deploy adds or changes a migration, you must apply it by hand.**

1. Open `database/migrations/` and find files added since the last deploy.
2. Translate each one to plain SQL in phpMyAdmin.
   - Keep the `Schema::hasColumn(...)` guards — they make the SQL safe to re-run.
   - Reuse `database/manual/*.sql` where a manual script already exists.
3. Run it against **both** the production and the sandbox database.
4. Optionally record it so it is not forgotten:
   ```sql
   INSERT INTO migrations (migration, batch) VALUES ('2026_10_16_000002_add_payroll_accounting_classification', 1);
   ```
   Only do this after the SQL has actually been applied.

> Symptom of a forgotten migration: a page that used to work starts returning
> `500 ... Unknown column 'x' in 'INSERT INTO'`. The code deployed fine; only the
> database is behind.

## 6. After each deploy — smoke tests

- [ ] Homepage + login works
- [ ] CSS/JS loaded (not unstyled)
- [ ] Create/edit an employee
- [ ] Run payroll on a SMALL batch (host kills scripts at 20 seconds!)
- [ ] Upload one attachment, download it back
- [ ] Reports render

## 7. Troubleshooting

| Symptom | Cause | Fix |
|---|---|---|
| Blank page / "autoload.php" error | vendor not uploaded | Check Actions log — composer step failed? |
| Site up but no styles | Vite build missing | Check `npm run build` step in Actions log |
| `500 ... Unknown column 'x' in 'INSERT INTO'` | Migration not applied to that DB | See section 5 — run the SQL in phpMyAdmin |
| Error 500 everywhere | `.htaccess` problem or bad `.env` | Set APP_DEBUG=true temporarily in the secret, redeploy, read error |
| Login loops back | `sessions` table missing | Re-import DB dump |
| 403 on some file | Root `.htaccess` guard — expected for `.env`, `.git`, etc. |
| Payroll dies mid-way | 20-second script kill (host limit) | Use smaller batches; cannot be fixed |

## Known accepted losses on free hosting

- Backup/Restore buttons error out (`exec()` banned)
- Laravel scheduled tasks do not run (no cron), including automatic absent marking and nightly cleanups
- Emails can't send (SMTP ports blocked)
- >50k visits/day = site sleeps until midnight

## Laravel scheduler requirement

The absent-marking command checks employee workdays and waits until each assigned shift ends. It is scheduled to run every 15 minutes, but Laravel's scheduler must itself be invoked every minute for scheduled commands to run.

On a Windows/XAMPP machine, configure Windows Task Scheduler to run `C:\xampp\php\php.exe` with `artisan schedule:run` as the argument, set the working directory to the project root, and repeat the task every minute. InfinityFree does not provide cron jobs, so automatic absent marking will not run there unless the host offers a scheduler or an external scheduler is configured.

## ⚠️ Inode budget warning

Account limit ≈ 30,000 files. Two installs (prod + test) ≈ 22k files. Delete the test install via File Manager once stable, re-upload when needed again.
