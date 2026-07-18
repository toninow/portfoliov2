# Despliegue

> No desplegar automáticamente. Esta guía describe los pasos manuales para producción.

## Requisitos del servidor

- PHP 8.3+ (FPM) con las extensiones listadas en el README.
- MySQL 8 (o MariaDB compatible).
- Composer, Node 20+ (para el build de assets), y un servidor web (Apache/Nginx).
- Certificado TLS (HTTPS).

## Pasos

1. **Código**: clona/actualiza el repositorio en el servidor.
2. **Dependencias**:

   ```bash
   composer install --no-dev --optimize-autoloader
   npm ci && npm run build
   ```

3. **Entorno**: crea `.env` a partir de `.env.example` y define:
   - `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://antoniobc.net`.
   - Credenciales `DB_*` de MySQL.
   - `ADMIN_PASSWORD` fuerte (primer administrador).
   - `MAIL_*` para las notificaciones del formulario de contacto.

   ```bash
   php artisan key:generate
   ```

4. **Base de datos**:

   ```bash
   php artisan migrate --force
   php artisan db:seed --force        # solo la primera vez / contenido inicial
   ```

5. **Storage**:

   ```bash
   php artisan storage:link
   ```

6. **Cachés de producción**:

   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

7. **Colas** (driver `database`): ejecuta un worker supervisado (systemd/supervisor):

   ```bash
   php artisan queue:work --tries=3
   ```

8. **Programador** (cron):

   ```cron
   * * * * * cd /ruta && php artisan schedule:run >> /dev/null 2>&1
   ```

## Permisos

`storage/` y `bootstrap/cache/` deben ser escribibles por el usuario de PHP-FPM.

## Servidor web

Apunta el document root a `public/`. Ejemplo Nginx: `try_files $uri $uri/ /index.php?$query_string;`
y pasa `.php` a PHP-FPM.

## Tras cada despliegue

```bash
composer install --no-dev --optimize-autoloader
npm run build
php artisan migrate --force
php artisan config:cache route:cache view:cache
php artisan queue:restart
```

## Rollback

Revertir al commit anterior y volver a ejecutar los pasos de cachés/migraciones. Realiza
copia de seguridad de la base de datos antes de migrar (ver `SECURITY.md`).
