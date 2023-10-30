#!/usr/bin/env bash
echo "Running composer"
composer clearcache
composer global require hirak/prestissimo

echo "Compose installing"
composer install --no-dev --working-dir=/var/www/html

echo "Caching config..."
php artisan config:cache

echo "Caching routes..."
php artisan route:cache

echo "Running migrations..."
php artisan migrate:fresh --seed

echo "Done deploying commands"
