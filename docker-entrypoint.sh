#!/bin/bash
set -e

# Clear any cached config first (in case env vars changed)
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true

# Generate app key if not set
if [ -z "$APP_KEY" ]; then
    echo "Generating APP_KEY..."
    php artisan key:generate --force
fi

# Run migrations
echo "Running migrations..."
php artisan migrate --force || echo "Migration failed, continuing..."

# Cache configuration (only if all env vars are set)
echo "Caching configuration..."
php artisan config:cache || echo "Config cache failed, using live config"
php artisan route:cache || echo "Route cache failed, using live routes"
php artisan view:cache || echo "View cache failed, using live views"

# Create storage link
php artisan storage:link || echo "Storage link already exists or failed"

# Set proper permissions
chmod -R 755 storage bootstrap/cache || true

# Ensure storage/logs is writable
mkdir -p storage/logs
chmod -R 777 storage/logs || true

# Start the application with error output
echo "Starting Laravel server on port ${PORT:-8080}..."
echo "APP_DEBUG=${APP_DEBUG:-false}"
echo "DB_CONNECTION=${DB_CONNECTION:-not set}"
echo "Checking database connection..."
php artisan db:show || echo "Database connection check failed"

exec php artisan serve --host=0.0.0.0 --port=${PORT:-8080} 2>&1

