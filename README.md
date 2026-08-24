# Smart Accounting v3

Payroll, HR, and remittance management system for transport operations — employees, drivers, routes, attendance, loans/cash advances, statutory deductions, payroll batches, 13th month pay, and reports.

**Stack:** Laravel 12 · PHP 8.3 · MySQL · Tailwind CSS 4 · Vite

**Live (production):** https://smartaccounting-v3.freedev.app
**Sandbox:** https://test.smartaccounting-v3.freedev.app *(deploy-test branch only)*

---

## ⚡ TL;DR for teammates

| Branch | Deploys to | Purpose |
|---|---|---|
| `deploy-test` | Sandbox site | Break things here freely |
| `main` | Production site | Only tested code goes here |

Push → GitHub Actions auto-builds & uploads. First deploy ~15–40 min, later ones faster.
Full runbook: **[DEPLOYMENT.md](DEPLOYMENT.md)**

---

## 💻 Local development

```bash
composer install
npm install
cp .env.example .env        # then fill DB creds + run: php artisan key:generate
php artisan migrate --seed  # optional seeders
php artisan serve           # http://127.0.0.1:8000
npm run dev                 # separate terminal, for live CSS/JS
```

---

## 🔑 GitHub Secrets (repo owner sets these once)

`FTP_USERNAME` · `FTP_PASSWORD` · `FTP_SERVER_DIR_PROD` · `FTP_SERVER_DIR_TEST` · `PROD_ENV_FILE` · `TEST_ENV_FILE`

Details: [DEPLOYMENT.md §2–3](DEPLOYMENT.md)

---

## 🚫 InfinityFree free-hosting limits — KNOW THESE

| Limit | Impact on us |
|---|---|
| **PHP scripts killed after 20 seconds** | Payroll on large batches dies mid-run → always test with SMALL employee sets |
| **`exec()` disabled** | Backup/Restore buttons in Super Admin will error — known, accepted |
| **No cron jobs** | Nightly cleanup tasks never run — old logs/notifications just accumulate |
| **SMTP ports blocked** | App cannot send real emails |
| **~50k visits/day** | Exceeding = site sleeps until midnight |
| **10 MB max per uploaded file** | Employee attachments above this fail |
| **~30,000 file limit** | Two installs ≈ 22k files — delete sandbox copy when idle |

## ✅ What works fine

Everything else: login, employees, attendance, leaves, loans, cash advances, payroll generation (small batches), reports, dashboards.

---

## 🔒 Security rules — non-negotiable

1. **NEVER commit `.env`** — it holds DB passwords. It is gitignored; keep it that way.
2. **Never commit credential files** (`username_password.txt` was removed from tracking for this reason).
3. Production `.env` lives ONLY in the GitHub secret `PROD_ENV_FILE`.
4. `APP_DEBUG=false` in production secrets — never leak stack traces.

---

## 🆘 Deploy broke? Quick triage

| Symptom | Fix |
|---|---|
| Blank page | composer step failed — check Actions log |
| No styles | Vite build failed — check Actions log |
| Error 500 | Temporarily set `APP_DEBUG=true` in the env secret, redeploy, read message |
| Login loops back | `sessions` table missing → re-import DB dump |

More: [DEPLOYMENT.md §6](DEPLOYMENT.md)
