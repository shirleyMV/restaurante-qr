# 🍽️ Sistema de Restaurante con QR

Sistema completo de gestión de restaurante con menú digital, pedidos por QR, panel de administración y punto de venta.

## ✨ Características

- 📱 **Menú Digital con QR**: Los clientes escanean el QR de la mesa y ordenan desde su celular
- 📊 **Panel de Administración**: Gestión completa con Filament PHP
- 👥 **Roles de Usuario**: Administrador y Cajera con permisos diferenciados
- 💳 **Múltiples Métodos de Pago**: Efectivo y QR
- 🖼️ **Gestión de Imágenes**: Productos con fotos
- 📦 **Control de Stock**: Inventario en tiempo real
- 🪑 **Gestión de Mesas**: Códigos QR únicos por mesa

## 🛠️ Tecnologías

- **Backend**: Laravel 11
- **Admin Panel**: Filament PHP 3
- **Base de Datos**: MySQL (local) / PostgreSQL (Render)
- **Frontend**: Livewire, Alpine.js, Tailwind CSS
- **Despliegue**: Render.com

---

# 🚀 Guía de Despliegue en Render

## ⚠️ Problema Resuelto

Esta guía soluciona el problema de que tu aplicación en Render no tenga:
- Productos con imágenes
- Usuarios (administrador y cajera)
- Mesas con QR
- Métodos de pago

## ✅ Cambios Realizados

### 1. Script de Build Actualizado
El archivo `render-build.sh` ahora ejecuta automáticamente el seeder que carga todos los datos iniciales.

### 2. Imágenes Incluidas
Se modificó el `.gitignore` para permitir que las imágenes de productos se suban al repositorio.

### 3. Seeder Mejorado
Se agregó el campo `stock` a todos los productos para que estén disponibles desde el inicio.

---

## 📋 Pasos para Desplegar

### Paso 1: Subir los Cambios a GitHub

```bash
# 1. Agregar todos los cambios (incluyendo imágenes)
git add .

# 2. Hacer commit
git commit -m "Configurar seeder e imágenes para Render"

# 3. Subir a GitHub
git push origin main
```

### Paso 2: Redesplegar en Render

**Opción A: Redespliegue Automático**
- Si tienes auto-deploy activado, Render detectará los cambios automáticamente
- Espera 5-10 minutos para que termine el build

**Opción B: Redespliegue Manual**
1. Ve a https://dashboard.render.com
2. Selecciona tu servicio `restaurante-qr`
3. Click en **"Manual Deploy"** → **"Deploy latest commit"**
4. Espera a que termine el build (verás los logs en tiempo real)

### Paso 3: Verificar el Despliegue

En los logs de Render deberías ver:
```
🗄️ Ejecutando migraciones...
🌱 Cargando datos iniciales (productos, usuarios, mesas)...
Usuario administrador creado exitosamente
Usuario cajera creado exitosamente
Productos creados exitosamente
Mesas creadas exitosamente
✅ Build completado!
```

---

## 🔐 Credenciales de Acceso

### Administrador
- **Email:** `admin@restaurante.com`
- **Contraseña:** `Admin123!`
- **Ruta:** `https://tu-app.onrender.com/admin`

### Cajera
- **Email:** `cajera@restaurante.com`
- **Contraseña:** `Cajera123!`
- **Ruta:** `https://tu-app.onrender.com/admin`

---

## 🍽️ Datos Incluidos

### Platos Principales (Stock: 50 unidades c/u)
- Locro sencillo - Bs. 3
- Locro con presa - Bs. 5
- Guiso de fideo - Bs. 8
- Salchipollo - Bs. 15
- Salchipapa - Bs. 10
- Pollo frito - Bs. 8
- Picante de pollo - Bs. 10
- Silpancho - Bs. 10

### Bebidas (Stock: 100 unidades c/u)
- Coca-Cola, Sprite, Fanta, Pepsi, 7UP, Simba
- Presentaciones: 500ml y 2L

### Otros
- **10 Mesas** con códigos QR generados automáticamente
- **Métodos de Pago**: Efectivo y QR

---

## ⚠️ Solución de Problemas

### Las imágenes no se ven

**Solución:**
```bash
# Verificar que las imágenes estén en el repositorio
git status

# Si no aparecen, forzar su inclusión:
git add -f public/imagenes/productos/*.jpg
git add -f public/imagenes/productos/*.webp
git commit -m "Agregar imágenes de productos"
git push origin main
```

### No puedo iniciar sesión como cajera

**Solución:**
1. Ve a Render Dashboard → tu servicio → **"Shell"**
2. Ejecuta:
   ```bash
   php artisan db:seed --force
   ```

### Los productos no tienen stock

**Solución:**
```bash
# En Render Shell (ADVERTENCIA: borra todos los datos)
php artisan migrate:fresh --seed --force
```

### Error de conexión a base de datos

**Solución:**
Verifica en Render Dashboard → Environment que existan:
- `DB_CONNECTION=pgsql`
- `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`

---

## 💻 Desarrollo Local

```bash
# 1. Clonar el repositorio
git clone <tu-repo>
cd restaurante-filament

# 2. Instalar dependencias
composer install
npm install

# 3. Configurar .env
cp .env.example .env
php artisan key:generate

# 4. Configurar base de datos en .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=restaurante_qr
DB_USERNAME=root
DB_PASSWORD=

# 5. Migrar y sembrar datos
php artisan migrate --seed

# 6. Crear enlace de storage
php artisan storage:link

# 7. Compilar assets
npm run dev

# 8. Iniciar servidor
php artisan serve
```

Accede a: http://localhost:8000

---

## 📞 Verificación Final

Después del despliegue, verifica:

- [ ] Puedes acceder a `/admin` con las credenciales de cajera
- [ ] Los productos aparecen en el panel de administración
- [ ] Las imágenes se ven correctamente
- [ ] Puedes crear un pedido desde el menú de cliente
- [ ] Las mesas tienen códigos QR generados

---

## 🔄 Actualizar Datos

Para agregar más productos:

1. Edita `database/seeders/DatabaseSeeder.php`
2. Agrega las imágenes en `public/imagenes/productos/`
3. Haz commit y push
4. Render desplegará automáticamente

**Nota:** El seeder usa `firstOrCreate()`, no duplicará productos existentes.

---

## 📝 Licencia

Este proyecto está bajo la licencia MIT.
