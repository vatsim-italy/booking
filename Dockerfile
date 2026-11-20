# syntax=docker/dockerfile:1

# Stage 1: PHP extensions
FROM php:8.4-apache AS ext
RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends libzip-dev libpng-dev libjpeg-dev libfreetype6-dev; \
    docker-php-ext-configure gd --with-freetype --with-jpeg; \
    docker-php-ext-install -j"$(nproc)" pdo_mysql zip gd bcmath pcntl; \
    ldd "$(php -r 'echo ini_get("extension_dir");')"/*.so \
        | awk '/=>/ { so = $(NF-1); if (index(so, "/usr/local/") == 1) next; gsub("^/(usr/)?", "", so); print so }' \
        | sort -u | xargs -r dpkg-query --search | cut -d: -f1 | sort -u > /runtime-deps.txt; \
    rm -rf /var/lib/apt/lists/*


# Stage 2: composer deps (git/unzip live and die here)
FROM ext AS vendor
RUN apt-get update && apt-get install -y --no-install-recommends git unzip \
    && rm -rf /var/lib/apt/lists/*
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /app
COPY composer.json composer.lock ./
COPY app/Helper.php app/Helper.php
RUN --mount=type=cache,target=/root/.composer/cache \
    composer install --no-dev --no-scripts --no-autoloader --no-interaction --no-progress
COPY . .
# storage/ is dockerignored, but package:discover boots providers that need the Blade cache path
RUN mkdir -p storage/framework/views storage/framework/cache storage/framework/sessions storage/logs bootstrap/cache \
    && composer dump-autoload --no-dev --optimize --no-interaction


# Stage 3: frontend assets. Copy only what the build reads, so PHP-only changes reuse this layer.
FROM node:24-alpine AS frontend
WORKDIR /app
COPY package.json package-lock.json ./
RUN --mount=type=cache,target=/root/.npm npm ci --no-audit --no-fund
COPY webpack.mix.js ./
COPY resources/ resources/
# PurgeCSS scans app/ (plugin default) and the pagination views (webpack.mix.js)
COPY app/ app/
COPY --from=vendor /app/vendor/laravel/framework/src/Illuminate/Pagination/resources/views/ vendor/laravel/framework/src/Illuminate/Pagination/resources/views/
RUN npm run build


# Stage 4: final runtime image
FROM php:8.4-apache
COPY --from=ext /runtime-deps.txt /runtime-deps.txt
RUN set -eux; \
    apt-get update; \
    xargs -r apt-get install -y --no-install-recommends < /runtime-deps.txt; \
    a2enmod rewrite; \
    rm -rf /var/lib/apt/lists/* /runtime-deps.txt

COPY --from=ext /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/
COPY --from=ext /usr/local/etc/php/conf.d/ /usr/local/etc/php/conf.d/

COPY ./apache/000-default.conf /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html
COPY --from=vendor /app/ ./
COPY --from=frontend /app/public/ ./public/

RUN mkdir -p bootstrap/cache storage/framework/cache storage/framework/sessions storage/framework/views storage/logs \
    && chown -R www-data:www-data bootstrap storage \
    && chmod -R 755 bootstrap storage \
    && php artisan storage:link

EXPOSE 80