FROM php:8.2-cli

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libpq-dev \
    libzip-dev \
    libicu-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar extensiones PHP necesarias
RUN docker-php-ext-configure intl && \
    docker-php-ext-install pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd zip intl

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Establecer directorio de trabajo
WORKDIR /app

# Copiar archivos del proyecto
COPY . .

# Instalar dependencias de Composer
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Dar permisos a directorios necesarios
RUN chmod -R 775 storage bootstrap/cache

# Exponer puerto
EXPOSE 8000

# Comando de inicio
CMD php artisan config:clear && \
    php artisan cache:clear && \
    php artisan migrate --force --no-interaction && \
    php artisan db:seed --force --no-interaction && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache && \
    php artisan storage:link || true && \
    php artisan serve --host=0.0.0.0 --port=${PORT:-8000}