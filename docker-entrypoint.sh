#!/bin/bash
set -e

# Generate app key if not set
php artisan key:generate --force || true

# Run migrations
php artisan migrate --force || true

# Cache configuration
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Create storage link
php artisan storage:link || true

# Start the application
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8080}

