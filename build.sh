#!/usr/bin/env bash

set -e

echo "Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

echo "Installing NPM dependencies..."
npm install

echo "Building assets..."
npm run build

echo "Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Running migrations..."
php artisan migrate --force

echo "Seeding database..."
php artisan db:seed --force

echo "Creating storage link..."
php artisan storage:link

echo "Build completed successfully!"