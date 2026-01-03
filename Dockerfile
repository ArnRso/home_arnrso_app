FROM dunglas/frankenphp:1-php8.4-bookworm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy composer files
COPY composer.json composer.lock symfony.lock ./

# Install PHP dependencies
RUN composer install --no-scripts --no-autoloader --prefer-dist --ignore-platform-reqs

# Copy application files
COPY . .

# Generate autoload files
RUN composer dump-autoload --optimize --ignore-platform-reqs

# Remove platform check file to avoid runtime PHP version errors
RUN rm -f vendor/composer/platform_check.php

# Import AssetMapper assets
RUN php bin/console importmap:install

# Compile AssetMapper manifest
RUN php bin/console asset-map:compile

# Copy Caddyfile
COPY Caddyfile /etc/caddy/Caddyfile

# Copy entrypoint script
COPY docker-entrypoint.sh /docker-entrypoint.sh
RUN chmod +x /docker-entrypoint.sh

# Create var and assets directories and set permissions
RUN mkdir -p var/cache var/log var/data assets/vendor public/assets \
    && chown -R www-data:www-data var assets public/assets \
    && chmod -R 775 var assets public/assets

# Expose ports
EXPOSE 80 443

# Set environment to production by default
ENV APP_ENV=prod

# Start application using entrypoint script
CMD ["/docker-entrypoint.sh"]
