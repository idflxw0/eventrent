#!/bin/sh
set -e

echo "==> Creating var/ directories..."
mkdir -p var/cache var/log

echo "==> Installing Composer dependencies..."
composer install --no-interaction --prefer-dist --no-scripts

echo "==> Generating autoloaders..."
composer dump-autoload --no-interaction

echo "==> Warming up Symfony cache..."
php bin/console cache:warmup

echo "==> Running migrations..."
php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration

echo "==> Starting PHP server on port 8000..."
exec php -S 0.0.0.0:8000 -t public
