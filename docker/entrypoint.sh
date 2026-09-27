#!/usr/bin/env sh
set -e

# Default PORT if not provided by Render environment
PORT="${PORT:-8080}"

# Substitute $PORT into Nginx configuration
sed -i "s/\${PORT}/${PORT}/g" /etc/nginx/conf.d/default.conf

# Ensure required storage and cache directories exist with correct permissions
mkdir -p /var/www/html/storage/framework/cache/data \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/app/documents \
         /var/www/html/storage/app/public \
         /var/www/html/storage/logs

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Production optimization if APP_KEY is provided
if [ -n "$APP_KEY" ]; then
    echo "Caching Laravel configuration and routes..."
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
fi

# Start PHP-FPM daemon
echo "Starting PHP-FPM..."
php-fpm -D

# Start Nginx in foreground on configured port
echo "Starting Nginx on port ${PORT}..."
exec nginx -g 'daemon off;'
