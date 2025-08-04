# Stage 1: Build frontend assets
FROM node:18-alpine as frontend

WORKDIR /app
COPY . .
RUN npm ci

COPY resources/ resources/
COPY public/ public/
RUN npm run build


# Stage 2: Main application image with Apache and PHP
FROM php:8.2-apache

# Enable Apache mods
RUN a2enmod rewrite

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git unzip zip libzip-dev libpng-dev libonig-dev libxml2-dev \
    libpq-dev libjpeg-dev libfreetype6-dev libicu-dev g++ ca-certificates \
    && docker-php-ext-install pdo pdo_mysql zip gd mbstring xml bcmath opcache pcntl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Copy Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy Apache config
COPY ./apache/000-default.conf /etc/apache2/sites-available/000-default.conf

# Set working directory
WORKDIR /var/www/html

# Copy app files
COPY . .

# Copy built frontend assets
COPY --from=frontend /app/public/ /var/www/html/public/

# Ensure Laravel cache paths exist and are writable
RUN mkdir -p bootstrap/cache storage/framework/cache storage/framework/sessions storage/framework/views storage/logs \
    && chown -R www-data:www-data bootstrap storage \
    && chmod -R 755 bootstrap storage

# Laravel setup
RUN composer install --no-dev --optimize-autoloader \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan storage:link

EXPOSE 80
CMD ["apache2-foreground"]
