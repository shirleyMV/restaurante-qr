#!/bin/bash

echo "🚀 Iniciando despliegue..."

# Instalar dependencias
echo "📦 Instalando dependencias..."
composer install --no-dev --optimize-autoloader --no-interaction

# Limpiar caché
echo "🧹 Limpiando caché..."
php artisan config:clear
php artisan cache:clear

# Ejecutar migraciones
echo "🗄️ Ejecutando migraciones..."
php artisan migrate --force --no-interaction

# Optimizar aplicación
echo "⚡ Optimizando aplicación..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Crear enlace simbólico de storage
echo "🔗 Creando enlace de storage..."
php artisan storage:link || true

# Iniciar servidor
echo "✅ Iniciando servidor..."
php artisan serve --host=0.0.0.0 --port=${PORT:-8000}