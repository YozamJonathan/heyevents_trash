# syntax=docker/dockerfile:1

# ==============================================================================
# Stage 1 — build
#
# This stage needs BOTH PHP and Node, which is unusual for a JS app but required
# here: `@laravel/vite-plugin-wayfinder` shells out to `artisan wayfinder:generate`
# during `npm run build` to generate type-safe route helpers, and
# `laravel-vite-plugin` reads the app's route list. So the build cannot run in a
# Node-only container.
#
# It is a throwaway stage — nothing from it ships except the final /app tree.
#
# Built `FROM php:8.4-fpm` rather than `php:8.4-cli` on purpose: the fpm image
# contains the `php` CLI too, so both stages share one set of base layers. Two
# base images would mean pulling and storing ~500 MB twice for no benefit.
# ==============================================================================
FROM php:8.4-fpm-bookworm AS builder

ENV DEBIAN_FRONTEND=noninteractive \
    COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_MEMORY_LIMIT=-1

# Node 22 matches CI (.github/workflows/tests.yml).
RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
        ca-certificates curl gnupg git unzip xz-utils \
        libicu-dev libzip-dev \
        libpng-dev libjpeg62-turbo-dev libfreetype6-dev libwebp-dev; \
    mkdir -p /etc/apt/keyrings; \
    curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key \
        | gpg --dearmor -o /etc/apt/keyrings/nodesource.gpg; \
    echo "deb [signed-by=/etc/apt/keyrings/nodesource.gpg] https://deb.nodesource.com/node_22.x nodistro main" \
        > /etc/apt/sources.list.d/nodesource.list; \
    apt-get update; \
    apt-get install -y --no-install-recommends nodejs; \
    rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# `pdo_mysql` is the important one here: mysqlnd is present in the base image but
# the pdo_mysql driver itself is not, so without this Laravel cannot talk to
# MySQL at all. opcache is deliberately absent — the official PHP image already
# ships and enables it.
RUN set -eux; \
    docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp; \
    docker-php-ext-install -j"$(nproc)" bcmath intl zip gd exif pdo_mysql

WORKDIR /app

# --- PHP dependencies -------------------------------------------------------
# Copied on their own so this layer is only invalidated when composer.lock moves.
COPY composer.json composer.lock ./
RUN composer install \
        --no-dev \
        --no-scripts \
        --no-autoloader \
        --prefer-dist \
        --no-interaction

# --- Node dependencies ------------------------------------------------------
# Same trick for package-lock.json. `npm ci` is used rather than `npm install`
# so the lockfile is authoritative and builds are reproducible.
COPY package.json package-lock.json .npmrc ./
RUN npm ci --no-audit --no-fund

COPY . .

# --- Compile ---------------------------------------------------------------
# The build must never reach a real database. A throwaway sqlite file plus
# array-backed session/cache/queue lets `artisan` boot cleanly inside the build.
RUN set -eux; \
    : > /tmp/build.sqlite; \
    export APP_KEY="base64:$(head -c 32 /dev/urandom | base64 -w0)" \
           APP_ENV=production \
           APP_DEBUG=false \
           DB_CONNECTION=sqlite \
           DB_DATABASE=/tmp/build.sqlite \
           SESSION_DRIVER=array \
           CACHE_STORE=array \
           QUEUE_CONNECTION=sync \
           MAIL_MAILER=log; \
    composer dump-autoload --optimize --no-dev; \
    php artisan package:discover --ansi; \
    npm run build; \
    rm -f /tmp/build.sqlite

# ==============================================================================
# Stage 2 — runtime
#
# nginx and PHP-FPM live in one container under supervisord. That is a deliberate
# choice over two containers: nginx has to read files straight off disk, and with
# a separate container that would mean sharing `public/` over a volume. Same-box
# means static assets are served locally with no volume juggling and no extra
# network hop. For a single small instance this is the simpler, more robust shape.
# ==============================================================================
FROM php:8.4-fpm-bookworm AS runtime

ENV DEBIAN_FRONTEND=noninteractive

RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
        nginx supervisor curl ca-certificates \
        libicu-dev libzip-dev \
        libpng-dev libjpeg62-turbo-dev libfreetype6-dev libwebp-dev; \
    rm -rf /etc/nginx/sites-enabled/*; \
    docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp; \
    docker-php-ext-install -j"$(nproc)" bcmath intl zip gd exif pdo_mysql; \
    rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

COPY --from=builder /app /var/www/html

COPY docker/nginx/default.conf /etc/nginx/sites-available/default
RUN ln -s /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default

COPY docker/php/php.ini     /usr/local/etc/php/conf.d/99-app.ini
COPY docker/php/zz-fpm.conf /usr/local/etc/php-fpm.d/zz-app.conf
COPY docker/supervisor/supervisord.conf /etc/supervisor/supervisord.conf
COPY docker/entrypoint.sh   /usr/local/bin/entrypoint
RUN chmod +x /usr/local/bin/entrypoint

# Wipe build-time caches: they hold absolute /app paths and a stale
# package manifest. The entrypoint regenerates them against /var/www/html.
RUN set -eux; \
    rm -f bootstrap/cache/*.php; \
    mkdir -p \
        bootstrap/cache \
        storage/app/public \
        storage/framework/cache/data \
        storage/framework/sessions \
        storage/framework/testing \
        storage/framework/views \
        storage/logs; \
    chown -R www-data:www-data storage bootstrap/cache; \
    chmod -R 775 storage bootstrap/cache

EXPOSE 80

HEALTHCHECK --interval=30s --timeout=5s --start-period=45s --retries=3 \
    CMD curl -fsS http://127.0.0.1/up || exit 1

ENTRYPOINT ["/usr/local/bin/entrypoint"]
CMD ["supervisord", "-c", "/etc/supervisor/supervisord.conf"]
