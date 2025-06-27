#!/bin/bash

# Laravolt Deployment Script Example
# This script shows how to properly deploy a Laravel application with Laravolt

set -e

echo "Starting Laravolt application deployment..."

# 1. Install/Update Composer dependencies
echo "Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader

# 2. The assets should be automatically extracted via Composer scripts
# If they're not, you can manually extract them:
# php artisan laravolt:extract-assets

# 3. Create symlinks (will preserve extracted assets if they exist)
echo "Creating symlinks..."
php artisan laravolt:link

# 4. Cache configuration (production optimization)
echo "Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. Run migrations
echo "Running migrations..."
php artisan migrate --force

# 6. Set proper permissions
echo "Setting permissions..."
chmod -R 775 storage bootstrap/cache
chmod -R 775 public/laravolt 2>/dev/null || true
chmod -R 775 resources/icons 2>/dev/null || true

echo "Deployment completed successfully!"
