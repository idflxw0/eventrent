#!/bin/sh
set -e

echo "==> Warming up Symfony cache (prod)..."
APP_ENV=prod php bin/console cache:warmup --no-debug

echo "==> Running migrations..."
php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration

echo "==> Starting PHP-FPM..."
exec "$@"
