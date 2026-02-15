# Deploying Trailer Renting System to cPanel

This guide walks you through deploying the Laravel app to a cPanel-hosted server.

---

## 1. Server requirements

- **PHP**: 8.2+ (check in cPanel → **Select PHP Version** or **MultiPHP Manager**)
- **Extensions**: `ctype`, `curl`, `dom`, `fileinfo`, `json`, `mbstring`, `openssl`, `pdo`, `tokenizer`, `xml`, `bcmath` (for number formatting)
- **MySQL** or **MariaDB** (create via cPanel → **MySQL® Databases**)
- **Composer** (often available via SSH or cPanel’s **Terminal**)

---

## 2. Where to put the application

Laravel must be served with the **document root** pointing at the `public` folder.

### Option A: App inside `public_html` (simple)

1. Create a folder for the app, e.g. `public_html/trailer-app` (or use `public_html` itself if this is the only site).
2. Upload/copy your project so that the Laravel **root** (where `artisan`, `app/`, `vendor/` are) is inside that folder.
3. In cPanel → **Domains** (or **Addon Domains** / **Subdomains**), set the **Document Root** to:
   - `public_html/trailer-app/public`  
   or  
   - `public_html/public`  
   so the web server only serves the `public` directory.

**Important:** The URL must **not** point at the folder that contains `app/` and `.env`. It must point at `public` only.

### Option B: App outside `public_html` (recommended)

1. Create a directory **outside** `public_html`, e.g. `trailer-app` in your home directory:
   ```
   ~/trailer-app/          ← full Laravel app here (app/, bootstrap/, public/, etc.)
   ~/public_html/          ← web-visible files
   ```
2. Set the **document root** for your domain to:
   ```
   ~/trailer-app/public
   ```
   (In cPanel this is often done under **Domains** → **Document Root** for the domain.)

This keeps `.env` and code outside the web root.

---

## 3. Upload the project

- **Git (if available):** SSH into the server, `cd ~/trailer-app`, then:
  ```bash
  git clone https://github.com/Nabot/Trailer-Rental-System.git .
  ```
- **Or:** Upload a zip of the project (excluding `node_modules`, `.env`, and ideally `.git` for size), then extract so `artisan` and `public/index.php` are in the right place.

---

## 4. Install dependencies (no dev packages)

From the Laravel root (e.g. `~/trailer-app`):

```bash
composer install --no-dev --optimize-autoloader
```

If Composer isn’t in your path, use the full path (e.g. `php /path/to/composer.phar install --no-dev --optimize-autoloader`).

---

## 5. Environment file

1. Copy the example env file:
   ```bash
   cp .env.example .env
   ```
2. Edit `.env` (cPanel **File Manager** or SSH):
   - `APP_NAME="Trailer Renting System"` (or your app name)
   - `APP_ENV=production`
   - `APP_DEBUG=false`
   - `APP_KEY=` → generate with `php artisan key:generate` (see below)
   - `APP_URL=https://yourdomain.com` (your real URL, with https if you use SSL)
   - `APP_TIMEZONE=Africa/Windhoek` (or your timezone)
   - Database:
     ```env
     DB_CONNECTION=mysql
     DB_HOST=127.0.0.1
     DB_PORT=3306
     DB_DATABASE=your_cpanel_db_name
     DB_USERNAME=your_cpanel_db_user
     DB_PASSWORD=your_cpanel_db_password
     ```
   - For production, keep `SESSION_DRIVER=database` and `CACHE_STORE=database` if you use MySQL (create DB and user in cPanel first).

3. Generate application key:
   ```bash
   php artisan key:generate --force
   ```

---

## 6. Database

1. In cPanel → **MySQL® Databases**:
   - Create a database (e.g. `cpaneluser_trailer`).
   - Create a user and assign it to that database (All Privileges).
   - Note the full DB name and username (often `cpaneluser_dbname`).

2. Run migrations:
   ```bash
   php artisan migrate --force
   ```

3. (Optional) Seed default data if you have seeders:
   ```bash
   php artisan db:seed --force
   ```

---

## 7. Storage and permissions

Laravel needs writable `storage` and `bootstrap/cache`, and a public link for uploaded files.

```bash
# Create storage link (for uploads, e.g. customer documents)
php artisan storage:link

# Permissions (use the user that runs PHP, often your cPanel user)
chmod -R 755 storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

If your host uses a different user for PHP (e.g. `nobody` or `apache`), you may need to set ownership:

```bash
chown -R $USER:www-data storage bootstrap/cache   # Linux; adjust group to what your host uses
# or
chown -R $USER:$USER storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

---

## 8. Caching for production

Run these after every code or config change:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

To clear them (e.g. after editing `.env`):

```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

---

## 9. Cron (scheduler, optional)

If you use `app/Console/Kernel.php` or Laravel’s scheduler, add one cron job in cPanel → **Cron Jobs**:

- **Frequency:** e.g. every minute: `* * * * *`
- **Command:**
  ```bash
  cd /home/your_cpanel_user/trailer-app && php artisan schedule:run >> /dev/null 2>&1
  ```
  Replace `/home/your_cpanel_user/trailer-app` with the full path to your Laravel root.

---

## 10. SSL (HTTPS)

- In cPanel → **SSL/TLS** or **Let’s Encrypt**, install a certificate for your domain.
- Set `APP_URL=https://yourdomain.com` in `.env` and ensure your site redirects to HTTPS (many hosts do this automatically; otherwise use middleware or server config).

---

## 11. Post-deploy checks

1. Open `https://yourdomain.com` (or your app URL) → should show login or dashboard (no 500 error).
2. Log in and test:
   - Create/edit customer (driver licence field).
   - Create/edit trailer (colour, load capacity, trailer value).
   - Create a booking and generate/download contract PDF.
3. Check `storage/logs/laravel.log` for errors (only if something fails).

---

## 12. Updating the app later

1. Pull new code or upload new files (overwrite except `.env`).
2. Run:
   ```bash
   composer install --no-dev --optimize-autoloader
   php artisan migrate --force
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```
3. If you use queues: `php artisan queue:restart` (if you run a queue worker).

---

## When you cannot change the document root

Some hosts do not allow changing the domain’s document root (e.g. it stays `public_html`). You can still run Laravel by using the **same directory** as both app root and document root and protecting sensitive paths.

**Setup:** Your Laravel app is already in `public_html` (you see `app/`, `vendor/`, `public/`, etc. in the “Index of /” listing).

**Required when using this workaround:** In `.env` add `PUBLIC_PATH_IS_APP_ROOT=true` so Laravel looks for the Vite manifest and assets in the document root (e.g. `public_html/build/`) instead of `public/build/`. After deploying, run `php artisan config:clear`.

**Steps:**

1. **Use the workaround files from the repo**  
   In the project there is a folder `deploy/cpanel-fix-docroot/` with:
   - `index.php` – front controller that uses the current directory as app root
   - `.htaccess` – URL rewriting + blocks access to `.env`, `app/`, `config/`, etc.

2. **On the server (SSH or cPanel File Manager)**  
   - Copy `deploy/cpanel-fix-docroot/index.php` to `public_html/index.php` (overwrite existing if present).
   - Copy `deploy/cpanel-fix-docroot/.htaccess` to `public_html/.htaccess` (overwrite).
   - If `public_html` already has an `index.php` or `.htaccess` from elsewhere, back them up first.

3. **Copy public assets** (so CSS/JS and storage link work)  
   - Copy everything inside `public_html/public/` (e.g. `build/`, favicon, etc.) into `public_html/` so that `public_html/build/` and similar exist.  
   - Or from SSH: `cp -r public_html/public/* public_html/` (then you can keep using `public_html/public/` for assets if you prefer; the new `index.php` lives in `public_html` and does not use the `public/` subfolder for bootstrapping).

4. **Storage link**  
   Run from Laravel root (`public_html`):  
   `php artisan storage:link`  
   so that `public_html/storage` points to `storage/app/public` for uploads.

5. **Test**  
   Visit `https://ironaxlena.com`. You should see the Laravel app (login/redirect), not the directory listing.  
   If you get 500, check `storage/logs/laravel.log` and that `storage` and `bootstrap/cache` are writable.

**Security:** The `.htaccess` in `deploy/cpanel-fix-docroot/` denies web access to `.env`, `app/`, `config/`, `database/`, `vendor/`, and other sensitive paths. Keep that file in place.

---

## Quick reference: document root

| Your setup              | Document root should be   |
|-------------------------|---------------------------|
| App in `public_html/trailer-app` | `public_html/trailer-app/public` |
| App in `~/trailer-app`  | `trailer-app/public` (full path as shown in cPanel) |

Never point the document root at the folder that contains `.env` and `app/`. Always point it at `public`.

---

## Troubleshooting

- **500 error / blank page:** Check `storage/logs/laravel.log` on the server (last lines show the real error). Ensure `storage` and `bootstrap/cache` are writable and `APP_KEY` is set.
- **500 after using the “cannot change document root” workaround:**
  1. **Restore the workaround files** – If you ran `cp -r public/* .` after copying the workaround files, it overwrote `index.php` and `.htaccess` with the ones from `public/`, which use `../` and break. Re-copy: `cp deploy/cpanel-fix-docroot/index.php index.php` and `cp deploy/cpanel-fix-docroot/.htaccess .htaccess` from the app root.
  2. **Check the log:** `tail -50 storage/logs/laravel.log` to see the exact exception.
  3. **Temporarily enable debug:** In `.env` set `APP_DEBUG=true`, reload the page to see the error on screen, then set it back to `false`.
  4. **Permissions:** `chmod -R 775 storage bootstrap/cache`.
- **404 on every route:** Document root must be the `public` folder. Ensure `public/.htaccess` exists and that Apache `mod_rewrite` is enabled (cPanel usually has it on).
- **DB connection error:** Double-check `.env` DB_* values. On cPanel, the database name is often prefixed (e.g. `cpaneluser_dbname`). Use `127.0.0.1` for `DB_HOST` unless your host says otherwise.
- **Storage link (404 on /storage/...):** Run `php artisan storage:link` from the Laravel root. If your host disallows symlinks, you may need to use a different approach (e.g. copy public storage into `public/storage` or use a different disk).
- **Mixed content (HTTPS site loading HTTP):** Set `APP_URL=https://...` and run `php artisan config:cache`.
