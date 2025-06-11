## Build stage for Node/Bun assets
#FROM oven/bun:latest AS node-builder
#WORKDIR /app
#COPY package.json bun.lock ./
#RUN bun install --frozen-lockfile
#COPY . .
#RUN bun run build
# uncoment above this if u wanna do build bun + php
# ill built it at my own machine cause it will be faster than im use prod server wwkwkw :v

# PHP stage
FROM dunglas/frankenphp

WORKDIR /app

# Install PHP extensions
RUN install-php-extensions \
    pcntl \
    pdo_pgsql \
    opcache \
    zip \
    gd \
    intl \
    bcmath \
    redis \
    mbstring \
    openssl \
    xml \
    imagick

# Configure PHP
COPY docker/php.ini /usr/local/etc/php/conf.d/custom.ini

# Install composer
COPY --from=composer /usr/bin/composer /usr/bin/composer

# Copy all application files first
COPY --chown=www-data:www-data . .

# Copy built assets from node-builder stage/ im now uncoment this cause i dont wanna to build bun with my docker, ill built it separetelly
#COPY --from=node-builder --chown=www-data:www-data /app/public/build public/build

# Copy built bun, off this if u want to build it straight with docker
COPY --chown=www-data:www-data public/build public/build

# Copy composer.lock and composer.json
COPY composer.lock composer.json ./

# Install PHP dependencies
RUN composer install --prefer-dist --no-scripts --no-dev --no-autoloader --optimize-autoloader
RUN composer dump-autoload --optimize

# Laravel specific commands
# Optimize Laravel
RUN php artisan optimize
RUN php artisan storage:link

# Set permissions
RUN chown -R www-data:www-data /app
RUN chmod -R 755 /app/storage /app/bootstrap/cache

# Expose port
EXPOSE 8000

# Start Laravel Octane
CMD ["php", "artisan", "octane:start", "--host=0.0.0.0", "--port=8000"]
