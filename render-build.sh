#!/usr/bin/env bash
# Render Build Script
# This script is used by Render for building the application

set -e

echo "🚀 Starting Render build process..."

# Install PHP dependencies
echo "📦 Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

# Install Node dependencies
echo "📦 Installing Node dependencies..."
npm ci

# Build frontend assets
echo "🏗️  Building frontend assets..."
npm run build

# Generate application key if not set
if [ -z "$APP_KEY" ]; then
    echo "🔑 Generating application key..."
    php artisan key:generate --force
fi

# Run migrations (only on first deploy or when needed)
# Uncomment the next line if you want migrations to run automatically
# php artisan migrate --force

# Cache configuration
echo "⚡ Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Create storage link
echo "🔗 Creating storage link..."
php artisan storage:link || true

echo "✅ Build completed successfully!"

