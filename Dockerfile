FROM php:8.4-cli

# Install Node.js from NodeSource
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash -

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libpq-dev \
    libicu-dev \
    zip \
    unzip \
    nodejs \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd zip intl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application
COPY . /var/www/html

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Install Node dependencies and build assets
RUN npm ci && npm run build

# Copy entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Configure PHP for large file uploads
RUN { \
    echo 'upload_max_filesize=50M'; \
    echo 'post_max_size=50M'; \
    echo 'max_execution_time=300'; \
    echo 'max_input_time=300'; \
    echo 'memory_limit=256M'; \
    echo 'max_file_uploads=20'; \
    } > /usr/local/etc/php/conf.d/uploads.ini

# Set permissions
RUN chmod -R 755 storage bootstrap/cache

# Expose port (Render will set PORT env var)
EXPOSE 8080

# Use entrypoint script
ENTRYPOINT ["docker-entrypoint.sh"]

