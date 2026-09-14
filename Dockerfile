# Stage 1: Build frontend assets with Vite (requires Node 20.19+ / 22.12+ for Vite 8)
FROM node:22-alpine AS frontend

WORKDIR /app

# Install JS deps first for better layer caching
COPY package.json package-lock.json ./
RUN npm ci

# Copy only what Vite needs to build
COPY vite.config.mjs ./
COPY resources/ resources/
COPY public/ public/

# Theme colors for Sass ($envColor*) — same defaults as vite.config.mjs / .env.example.
# Override at build time with e.g.:
#   docker build --build-arg BOOTSTRAP_COLOR_PRIMARY=#123456 .
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

RUN npm run build


# Stage 2: Main application image with Apache and PHP (must satisfy composer.json: php ^8.4)
FROM php:8.4-apache

# Enable Apache mods
RUN a2enmod rewrite

# Install system dependencies (libicu-dev already present so we can enable intl too)
RUN apt-get update && apt-get install -y \
    git unzip zip libzip-dev libpng-dev libonig-dev libxml2-dev \
    libpq-dev libjpeg-dev libfreetype6-dev libicu-dev g++ ca-certificates \
    && docker-php-ext-install pdo pdo_mysql zip gd mbstring xml bcmath opcache pcntl intl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Copy Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy Apache config
COPY ./apache/000-default.conf /etc/apache2/sites-available/000-default.conf

# Set working directory
WORKDIR /var/www/html

# Copy app files (vendor/ + node_modules/ excluded via .dockerignore)
COPY . .

# Copy built frontend assets (Vite manifest + hashed bundles in public/build)
COPY --from=frontend /app/public/build/ /var/www/html/public/build/

# Ensure Laravel cache paths exist and are writable
RUN mkdir -p bootstrap/cache storage/framework/cache storage/framework/sessions storage/framework/views storage/logs \
    && chown -R www-data:www-data bootstrap storage \
    && chmod -R 755 bootstrap storage

# Laravel setup (no package:discover cache surprise — it must run on container start,
# after the real .env is mounted; see docker-compose command below)
RUN composer install --no-dev --optimize-autoloader \
    && php artisan storage:link \
    && rm -f bootstrap/cache/*.php

EXPOSE 80
