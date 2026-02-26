#!/bin/bash

# Exit on error
set -e

# Clear all caches
echo "Clearing caches..."
php artisan optimize:clear

# Run migrations
echo "Running migrations..."
php artisan migrate --force

# Ensure storage permissions and structure
echo "Fixing storage permissions and structure..."
mkdir -p /var/www/html/storage/app/public
mkdir -p /var/www/html/storage/framework/cache
mkdir -p /var/www/html/storage/framework/cache/data
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/testing
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/logs

chown -R www-data:www-data /var/www/html/storage
chmod -R 775 /var/www/html/storage

# Storage link
echo "Creating storage link..."
php artisan storage:link

# Cache config and routes
echo "Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run Indexer in background (to not block Supervisor/Nginx startup)
# echo "Starting indexer in background..."
# (php artisan indexer:index) &



# Start Supervisor
echo "Starting Supervisor..."
/usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
