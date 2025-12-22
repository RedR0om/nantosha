#!/bin/bash
set -e

echo "🚀 Starting Nantosha application..."

# Clear caches
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true

# Generate app key if not set
if [ -z "$APP_KEY" ]; then
    echo "🔑 Generating APP_KEY..."
    php artisan key:generate --force
fi

# Run migrations
echo "📊 Running migrations..."
if php artisan migrate --force; then
    echo "✓ Migrations completed successfully"
else
    echo "✗ Migration failed - check database connection"
    php artisan db:show || true
fi

# Run seeders
if [ "${SEED_DATABASE:-true}" = "true" ]; then
    echo "🌱 Running database seeders..."
    if php artisan db:seed --force; then
        echo "✓ Database seeding completed successfully"
        echo "📈 Seeded data summary:"
        php artisan tinker --execute="echo '  - Categories: ' . App\Models\Category::count() . PHP_EOL; echo '  - Brands: ' . App\Models\Brand::count() . PHP_EOL; echo '  - Products: ' . App\Models\Product::count() . PHP_EOL; echo '  - FAQs: ' . App\Models\Faq::count() . PHP_EOL;" 2>/dev/null || true
    else
        echo "✗ Database seeding failed"
    fi
else
    echo "⏭️  Skipping database seeding (SEED_DATABASE=false)"
fi

# Cache configuration
echo "⚡ Caching configuration..."
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Create storage link
php artisan storage:link || true

# Set permissions
chmod -R 755 storage bootstrap/cache || true
mkdir -p storage/logs
chmod -R 777 storage/logs || true

# Start the application
echo "🌐 Starting Laravel server on port ${PORT:-8080}..."
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8080}

