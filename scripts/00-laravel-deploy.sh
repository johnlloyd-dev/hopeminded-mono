#!/usr/bin/env bash
echo "Running composer"
composer clearcache
composer global require hirak/prestissimo

echo "composer install"
composer install

echo "Caching config..."
php artisan config:cache

echo "Caching routes..."
php artisan route:cache

echo "Running migrations..."
php artisan migrate --force
php artisan db::seed

echo "done deploying commands"
