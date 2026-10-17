#!/bin/sh
set -e

# Sync fresh compiled assets from image template to public volume
if [ -d /var/www/public_template ]; then
    cp -ru /var/www/public_template/* /var/www/public/ 2>/dev/null || true
fi

# Ensure storage subdirectories exist
mkdir -p /var/www/storage/framework/cache/data
mkdir -p /var/www/storage/framework/sessions
mkdir -p /var/www/storage/framework/views
mkdir -p /var/www/storage/logs
mkdir -p /var/www/storage/app/public
mkdir -p /var/www/bootstrap/cache

# Fix ownership and permissions
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache /var/www/public
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Create storage symlink if missing
if [ ! -L /var/www/public/storage ]; then
    php artisan storage:link --force || true
fi

# Run the provided command
exec "$@"
