#!/bin/sh
set -e

echo "🚀 Starting application..."

# Wait a bit for any dependencies (if needed)
sleep 1

# Run database migrations
echo "📦 Running database migrations..."
php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration

# Clear and warm up cache
echo "🔥 Warming up cache..."
php bin/console cache:clear
php bin/console cache:warmup

echo "✅ Application ready!"

# Start FrankenPHP
exec frankenphp run --config /etc/caddy/Caddyfile
