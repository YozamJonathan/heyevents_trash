#!/usr/bin/env bash
#
# Server-side deploy for the non-Docker EC2 setup.
#
# Invoked over SSH by .github/workflows/deploy.yml, and safe to run by hand:
#
#   bash scripts/deploy-remote.sh
#
# Assumes a one-time server bootstrap has already happened (PHP 8.4 FPM, Node 22,
# Composer, MySQL, Nginx) and that the app is checked out at $APP_DIR with a
# populated .env.
#
# Idempotent: re-running it is how you apply a new commit or a config change.
set -euo pipefail

APP_DIR="${APP_DIR:-/var/www/heyevent}"
PHP_SVC="${PHP_SVC:-php8.4-fpm}"
BRANCH="${BRANCH:-main}"

cd "$APP_DIR"

echo "==> Fetching origin/$BRANCH"
git fetch --prune origin
git reset --hard "origin/$BRANCH"

echo "==> Installing PHP dependencies"
# Composer on a 1 GB instance will otherwise hit PHP's memory_limit.
export COMPOSER_MEMORY_LIMIT=-1
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

echo "==> Building frontend assets"
# public/build is gitignored, so it is produced here on every deploy.
npm ci
npm run build

echo "==> Running migrations"
php artisan migrate --force --no-interaction

echo "==> Linking storage"
php artisan storage:link --force >/dev/null 2>&1 || true

echo "==> Caching config, routes and views"
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Restarting queue workers"
php artisan queue:restart

echo "==> Fixing writable-directory permissions"
sudo chown -R ubuntu:www-data storage bootstrap/cache
sudo find storage bootstrap/cache -type d -exec chmod 2775 {} \;
sudo find storage bootstrap/cache -type f -exec chmod 664 {} \;

echo "==> Reloading PHP-FPM"
sudo systemctl reload "$PHP_SVC"

echo "Deploy complete."
