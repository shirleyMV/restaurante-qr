#!/bin/bash

set -e

echo "🔧 Instalando dependencias..."
composer install --no-dev --optimize-autoloader --no-interaction

echo "🗄️ Ejecutando migraciones..."
php artisan migrate --force --no-interaction

echo "⚡ Optimizando..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "🔗 Storage link..."
php artisan storage:link || true

echo "✅ Build completado!"