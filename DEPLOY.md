# Deploying to InfinityFree: Axiom Membership System

**Timebox:** 60 minutes, hard stop at 90. If it isn't working by then, demo locally. Live hosting is not a rubric item.

InfinityFree is a free demo host. It has **no SSH, no Composer, no `php artisan`, no symlinks, and no queue worker or scheduler**, and its web File Manager has small upload limits. Everything below is built around those limits. Your project already avoids queues, jobs, schedulers, and file uploads, so it fits.

---

## Step 0: Feasibility check (10 min, do it any time before Phase 9)

Don't spend an hour building a deployment on a host that can't run your app.

1. Create an InfinityFree account and a hosting account (you'll get a free subdomain).
2. In the control panel, find the **PHP version** setting. Your Laravel version needs PHP 8.2 or higher (check `composer.json` → `require.php`). If the host can't offer that, stop and demo locally.
3. Create a **MySQL database** in "MySQL Databases". Write down the **host name** (something like `sqlXXX.infinityfree.com`, *not* `localhost`), the full database name, username, and password. All of them carry an `if0_...` prefix.
4. Check whether FTP works (FileZilla with the FTP details from the panel). If it does, use it for uploads, since it's much easier than File Manager. If it doesn't, use File Manager with zips.

---

## Step 1: Make the project hosting-friendly (before you build features)

These are already in `AI_GUARDRAILS.md`, but double-check:
- Collation set to `utf8mb4_unicode_ci` in `config/database.php`. MySQL 8 defaults to `utf8mb4_0900_ai_ci`, which many free hosts reject on import.
- No dependence on `php artisan` at runtime, `storage:link`, queues, or the scheduler.
- Sessions and cache can run on the `file` driver.

---

## Step 2: Build a clean deploy copy

Don't touch your dev folder. Build from a fresh clone so dev dependencies never get uploaded.

```bash
git clone https://github.com/YOUR-USERNAME/axiom-membership.git axiom-deploy
cd axiom-deploy
composer install --no-dev --optimize-autoloader
npm ci && npm run build            # creates public/build (GSAP is bundled in here)
php artisan key:generate --show    # prints a key: copy it, you'll paste it into .env
```

`public/build` is gitignored, which is why we build it here. Without it, pages will fail with a Vite manifest error.

---

## Step 3: Lay out the files on the server

Everything on InfinityFree lives in `htdocs/`, which is the public web root. Keep the Laravel code in a subfolder and only the public files at the top.

```text
htdocs/
├── index.php            ← from public/index.php (edited, see Step 4)
├── .htaccess            ← from public/.htaccess
├── build/               ← from public/build/
├── favicon.ico, robots.txt (if present)
└── laravel-app/         ← EVERYTHING else
    ├── .htaccess        ← deny-all (Step 5)
    ├── .env             ← production values (Step 6)
    ├── app/  bootstrap/  config/  database/  resources/  routes/
    ├── storage/         ← keep the empty subfolders (see below)
    ├── vendor/
    ├── artisan  composer.json  composer.lock
```

Notes:
- **Delete** the default `index2.html` that InfinityFree puts in `htdocs/`.
- Don't upload `node_modules/`, `.git/`, `tests/`, or the `public/` folder itself into `laravel-app/` (its contents go to `htdocs/`).
- `storage/` needs these folders to exist: `framework/cache`, `framework/sessions`, `framework/views`, `logs`. Create them if empty folders got dropped. If you get permission errors, set them to 755 or 775 in File Manager.
- If you use File Manager, zip in two parts so each stays under the upload limit: one zip for `vendor/` (split further if it's too big) and one for everything else. Extract on the server.

---

## Step 4: Edit `htdocs/index.php`

Two changes: point at the new folder, and tell Laravel where the public folder actually is. The second one is easy to miss, and without it `@vite` can't find `build/manifest.json`.

```php
<?php
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

if (file_exists($maintenance = __DIR__.'/laravel-app/storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/laravel-app/vendor/autoload.php';

$app = require_once __DIR__.'/laravel-app/bootstrap/app.php';
$app->usePublicPath(__DIR__);   // public files live here, not in laravel-app/public

$app->handleRequest(Request::capture());
```

(If your Laravel version's `index.php` differs slightly, keep its structure. Only change the three `../` paths and add the `usePublicPath` line.)

---

## Step 5: Protect your secrets

Because `laravel-app/` is inside the web root, `.env` could be downloaded by anyone who guesses the URL. Create `htdocs/laravel-app/.htaccess` with:

```apache
<IfModule mod_authz_core.c>
    Require all denied
</IfModule>
<IfModule !mod_authz_core.c>
    Order deny,allow
    Deny from all
</IfModule>
```

After deploying, visit `https://YOURSITE/laravel-app/.env` in a browser. It **must** return 403 or an error, never the file contents. If it shows anything, fix this before anything else.

---

## Step 6: Production `.env`

Create `htdocs/laravel-app/.env`:

```env
APP_NAME="Axiom Membership"
APP_ENV=production
APP_KEY=base64:PASTE_THE_KEY_FROM_STEP_2
APP_DEBUG=false
APP_URL=https://YOURNAME.infinityfreeapp.com

LOG_CHANNEL=single
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=sqlXXX.infinityfree.com
DB_PORT=3306
DB_DATABASE=if0_XXXXXXXX_axiom_membership
DB_USERNAME=if0_XXXXXXXX
DB_PASSWORD=YOUR_DB_PASSWORD

SESSION_DRIVER=file
CACHE_STORE=file        # use CACHE_DRIVER=file on Laravel 10 and earlier
QUEUE_CONNECTION=sync
```

Use `http://` in `APP_URL` until the free SSL certificate is active in the control panel, then switch to `https://`.

---

## Step 7: Database: export locally, import on the host

You can't run `migrate --seed` on the server, so move the finished database instead.

1. Locally: `php artisan migrate:fresh --seed` (clean data, admin user, demo members).
2. Export: `mysqldump -u root -p axiom_membership > axiom_membership.sql` (or phpMyAdmin → Export).
3. Open the file and, if present, find and replace `utf8mb4_0900_ai_ci` with `utf8mb4_unicode_ci`. Delete any `CREATE DATABASE` or `USE` lines.
4. In the InfinityFree panel, open **phpMyAdmin**, select your `if0_...` database, **Import** the file.
5. Confirm all tables exist and the `users` table has your admin row.

If the schema changes later, export and re-import. Drop the old tables first.

---

## Step 8: Verify

1. `https://YOURSITE/` loads the login page with styling (CSS and JS present).
2. Log in with the seeded admin.
3. Run the **demo script** from `PLAN.md` section 10 end to end, including a payment through the simulated gateway and the printable receipt.
4. Check the GSAP animations run and that the page still works when you reload.
5. Confirm `APP_DEBUG=false`, and that `/laravel-app/.env` is blocked.

---

## Troubleshooting

| Symptom | Likely cause and fix |
| --- | --- |
| 500 error, blank page | Missing or invalid `APP_KEY`; unwritable `storage/` or missing subfolders; wrong path in `index.php`. Set `APP_DEBUG=true` **temporarily** to see the error, then set it back to `false`. |
| 403 error | No `index.php` in `htdocs/`, or `index2.html` still there |
| "Vite manifest not found" | `build/` wasn't uploaded to `htdocs/build/`, or the `usePublicPath(__DIR__)` line is missing |
| Unstyled page, no CSS/JS | `APP_URL` doesn't match the real URL (http vs https), or `build/` is incomplete |
| "Class not found" or autoload errors | `vendor/` upload was incomplete. Re-extract or re-upload it |
| Database connection refused | `DB_HOST` is still `localhost`. Use the `sqlXXX...` host from the panel |
| "Unknown collation" on import | Replace `utf8mb4_0900_ai_ci` in the dump (Step 7) |
| 419 Page Expired on forms | `storage/framework/sessions` missing or unwritable, or http/https mismatch with `APP_URL` |
| `open_basedir` warnings | A path points outside `htdocs/`. Re-check `index.php` and `.env` paths |

---

## Updating after the first deploy

- Changed PHP, routes, or views: re-upload only those files (`app/`, `routes/`, `resources/views/`).
- Changed JS or CSS: run `npm run build` in the deploy copy and re-upload `build/`.
- Changed the schema: re-export, then re-import, as in Step 7.

## Safety net

Keep the app runnable locally and keep screenshots of every page in `/docs`. Free hosts can be slow or briefly unavailable, and your demo shouldn't depend on one. Don't enter real personal data into the hosted copy.
