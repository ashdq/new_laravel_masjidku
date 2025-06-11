# Stage 1: Composer dependencies
FROM composer:2.6 as composer

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

COPY . .
RUN composer dump-autoload --optimize

# Stage 2: Node dependencies and build assets
FROM node:18-alpine as node

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY . .
RUN npm run build

# Stage 3: PHP application
FROM php:8.2-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    linux-headers \
    bash \
    nginx \
    supervisor \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    mysql \
    mysql-client

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql zip exif pcntl gd

# Create necessary directories
RUN mkdir -p /var/log/supervisor \
    && mkdir -p /var/run/php-fpm \
    && mkdir -p /var/log/nginx \
    && mkdir -p /var/log/php \
    && mkdir -p /var/lib/mysql

# Configure nginx
COPY docker/nginx.conf /etc/nginx/http.d/default.conf

# Configure PHP-FPM
COPY docker/php.ini /usr/local/etc/php/conf.d/local.ini

# Configure supervisord
COPY docker/supervisord.conf /etc/supervisord.conf

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY --from=composer /app .
COPY --from=node /app/public/build ./public/build

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/log/supervisor \
    && chown -R www-data:www-data /var/log/nginx \
    && chown -R www-data:www-data /var/log/php \
    && chown -R mysql:mysql /var/lib/mysql

# Initialize MySQL
RUN mysql_install_db --user=mysql --datadir=/var/lib/mysql

# Expose port 80
EXPOSE 80

# Start supervisord
CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisord.conf"] 