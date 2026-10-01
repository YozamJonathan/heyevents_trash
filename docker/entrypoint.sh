#!/bin/sh
# Runs on every container start, immediately before supervisord hands off to
# nginx + PHP-FPM. Everything here must be idempotent: containers get rebuilt
# and restarted far more often than anyone intends.
set -eu

cd /var/www/html

# `storage` is a volume, so ownership cannot be baked into the image layer.
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true

# Block until the database accepts TCP connections. Compose already gates the
# app on MySQL's healthcheck, so this is only a safety net for the case where
# MySQL is up but still running its first-boot initialisation.
if [ "${DB_CONNECTION:-}" = "mysql" ] || [ "${DB_CONNECTION:-}" = "mariadb" ] || [ "${DB_CONNECTION:-}" = "pgsql" ]; then
    DB_HOST="${DB_HOST:-mysql}"
    DB_PORT="${DB_PORT:-3306}"
    echo "entrypoint: waiting for database at ${DB_HOST}:${DB_PORT}"
    php -r '
        $host = getenv("DB_HOST");
        $port = (int) getenv("DB_PORT");
        for ($i = 0; $i < 60; $i++) {
            $sock = @fsockopen($host, $port, $errno, $errstr, 2);
            if ($sock) {
                fclose($sock);
                echo "entrypoint: database is accepting connections\n";
                exit(0);
            }
            sleep(2);
        }
        fwrite(STDERR, "entrypoint: database unreachable after 120s\n");
        exit(1);
    '
fi

# Links public/storage -> storage/app/public. Needed for any user-uploaded
# imagery to be servable. --force makes a repeat call a no-op.
php artisan storage:link --force >/dev/null 2>&1 || true

echo "entrypoint: starting: $*"
exec "$@"
