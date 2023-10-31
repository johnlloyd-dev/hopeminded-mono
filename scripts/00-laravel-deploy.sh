#!/usr/bin/env bash
echo "Running composer"
composer clear-cache
composer self-update

echo "Compose installing"
composer install --no-dev --working-dir=/var/www/html

echo "Caching config..."
php artisan config:cache

echo "Caching routes..."
php artisan route:cache

echo "Running migrations..."
php artisan migrate:fresh --seed

npm install

npm run build

npm run production

echo "Done deploying commands"
