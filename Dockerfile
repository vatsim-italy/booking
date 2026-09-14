# Stage 1: Build frontend assets with bun
FROM node:24-alpine AS frontend

WORKDIR /app

RUN curl -fsSL https://bun.com/install | bash
ENV PATH="${PATH}:/root/.bun/bin"

# Install deps first for better layer caching
COPY package.json bun.lockb ./
RUN bun install

# Copy only what Vite needs to build
COPY vite.config.mjs ./
COPY resources/ resources/
COPY public/ public/

# Theme colors for Sass ($envColor*) — override at build time if needed
ARG BOOTSTRAP_COLOR_PRIMARY=#2C3E50
ARG BOOTSTRAP_COLOR_SECONDARY=#95a5a6
ARG BOOTSTRAP_COLOR_TERTIARY=#18BC9C
ARG BOOTSTRAP_COLOR_SUCCESS=#18BC9C
ARG BOOTSTRAP_COLOR_WARNING=#F39C12
ARG BOOTSTRAP_COLOR_DANGER=#E74C3C
ENV BOOTSTRAP_COLOR_PRIMARY=$BOOTSTRAP_COLOR_PRIMARY \
    BOOTSTRAP_COLOR_SECONDARY=$BOOTSTRAP_COLOR_SECONDARY \
    BOOTSTRAP_COLOR_TERTIARY=$BOOTSTRAP_COLOR_TERTIARY \
    BOOTSTRAP_COLOR_SUCCESS=$BOOTSTRAP_COLOR_SUCCESS \
    BOOTSTRAP_COLOR_WARNING=$BOOTSTRAP_COLOR_WARNING \
    BOOTSTRAP_COLOR_DANGER=$BOOTSTRAP_COLOR_DANGER

RUN bun run build

# Stage 2: laravel-base runtime
FROM ghcr.io/vatsim-italy/laravel-base:latest

USER root
WORKDIR /var/www

# Install nginx + extensions missing from laravel-base
RUN apt-get update && apt-get install -y nginx \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install xml intl opcache

# Install PHP dependencies first (cache layer)
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --prefer-dist \
    --no-interaction

# Copy Laravel app
COPY . .

# Copy built assets
COPY --from=frontend /app/public/build public/build

# Laravel permissions
RUN mkdir -p storage bootstrap/cache \
    && chown -R www-data:www-data /var/www \
    && chmod -R 775 storage bootstrap/cache

# Copy nginx config
COPY docker/nginx/default.conf /etc/nginx/sites-available/default

CMD sh -c "\
  until php artisan db:monitor; do echo 'Waiting for DB...'; sleep 2; done && \
  service nginx start && \
  php artisan schedule:work & \
  php-fpm -F"