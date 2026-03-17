# Deploy notes

## When document root = app root (e.g. cPanel `public_html`)

If your domain’s document root is the Laravel project root (not the `public` folder):

1. Use the files in `cpanel-fix-docroot/` (e.g. copy `index.php` and `.htaccess` to your document root).
2. In `.env` set:
   ```env
   PUBLIC_PATH_IS_APP_ROOT=true
   ```
3. Trailer photos and other public-disk uploads are then served from `/storage/app/public/...` (no `php artisan storage:link` needed).
4. Run `php artisan config:clear` after changing `.env`.

If you use a different document root setup, you can set `STORAGE_URL_PREFIX` in `.env` to override how storage asset URLs are built (see `config/app.php`).
