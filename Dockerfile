# Stage 1: Build Vite frontend assets
FROM node:20-alpine AS frontend
WORKDIR /app
COPY package*.json vite.config.js ./
COPY resources ./resources
COPY public ./public
RUN npm install && npm run build

# Stage 2: Production PHP-FPM + Nginx application container
FROM php:8.2-fpm

# Install system dependencies & Nginx
RUN apt-get update && apt-get install -y \
    nginx \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libicu-dev \
    libonig-dev \
    unzip \
    curl \
    git \
    && rm -rf /var/lib/apt/lists/*

# Configure & install PHP extensions required by Laravel 12 & Razorpay
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_mysql \
        pdo_sqlite \
        mbstring \
        gd \
        zip \
        bcmath \
        intl \
        opcache

# Copy Composer binary from official image
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application source code
COPY . .

# Copy compiled frontend assets from Stage 1
COPY --from=frontend /app/public/build ./public/build

# Install production PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Copy custom Nginx & PHP configurations
COPY docker/nginx.conf.template /etc/nginx/conf.d/default.conf
COPY docker/php.ini /usr/local/etc/php/conf.d/custom.ini

# Set correct ownership for storage & bootstrap/cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Copy entrypoint script and set executable permissions
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 8080

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
