# Antonio Benalcázar — Portafolio + CMS + CRM

Portafolio profesional de Antonio Benalcázar (desarrollo de software, automatización e
integraciones empresariales) construido como una aplicación Laravel administrable, con panel
de administración Filament y un CRM ligero para gestionar contactos.

Sustituye a la antigua web estática (HTML/CSS/Bootstrap), que se conserva íntegra en
[`legacy-portfolio/`](legacy-portfolio/) como referencia de contenido e inventario.

## Stack

| Capa | Tecnología |
| --- | --- |
| Framework | Laravel 13 (PHP 8.3+) |
| Admin | Filament 4 (Livewire 3, incluido por Filament) |
| Frontend | Blade + Tailwind CSS 4 + Vite + Alpine (mínimo) |
| Traducciones | `spatie/laravel-translatable` (columnas JSON ES/EN) |
| Slugs | `spatie/laravel-sluggable` |
| Base de datos | MySQL en producción · SQLite en desarrollo/tests |
| Tests | PHPUnit |
| Formato | Laravel Pint |

> Nota de compatibilidad: la petición original indicaba **Filament 5 / Livewire 4 / Tailwind 4**.
> A la fecha de desarrollo, Filament 5 y Livewire 4 aún no tienen versión estable, por lo que se
> usa **Filament 4** (que incorpora Livewire 3). Tailwind CSS **4** sí se usa. El código sigue las
> convenciones vigentes y es actualizable cuando esas versiones se publiquen.

## Requisitos

- PHP >= 8.3 con extensiones: `pdo`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`,
  `json`, `bcmath`, `fileinfo`, `curl`, `gd` (o `imagick`), y `pdo_sqlite` / `pdo_mysql`.
- Composer 2.
- Node.js 20+ (probado con 22 LTS) y npm.
- Chrome/Chromium (opcional, solo para regenerar las capturas de QA).

## Instalación

```bash
git clone <repo> antoniobc && cd antoniobc

composer install
npm install

cp .env.example .env
php artisan key:generate

# Enlace de almacenamiento público (imágenes, CV)
php artisan storage:link
```

### Configuración

Edita `.env`. Para desarrollo rápido con SQLite:

```dotenv
DB_CONNECTION=sqlite
# crea el fichero de base de datos
```

```bash
touch database/database.sqlite
```

Para producción (MySQL):

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=antoniobc
DB_USERNAME=antoniobc
DB_PASSWORD=********
ADMIN_PASSWORD=<contraseña-fuerte-del-primer-admin>
MAIL_MAILER=smtp   # el formulario notifica al admin por correo
```

### Migraciones y seeders

```bash
php artisan migrate --seed        # crea el esquema y carga contenido inicial
```

El seeder `PortfolioSeeder` migra el contenido aprovechable de la web antigua (perfil,
tecnologías, proyectos históricos) y crea los proyectos destacados y servicios editables.

### Primer administrador

`DatabaseSeeder` crea un usuario `super_admin`:

- Email: `admin@antoniobc.net`
- Contraseña: valor de `ADMIN_PASSWORD` en `.env` (por defecto `password` — **cámbialo**).

También puedes crear uno manualmente:

```bash
php artisan tinker --execute="\App\Models\User::create(['name'=>'Admin','email'=>'you@example.com','password'=>bcrypt('secret'),'role'=>'super_admin','email_verified_at'=>now()]);"
```

Accede en `/admin`.

## Desarrollo

```bash
php artisan serve            # http://127.0.0.1:8000
npm run dev                  # Vite en modo watch (HMR)
```

## Build de producción

```bash
npm run build               # compila CSS/JS a public/build
php artisan config:cache route:cache view:cache
```

## Pruebas

```bash
php artisan test            # PHPUnit (usa SQLite en memoria)
./vendor/bin/pint           # formato de código
```

## Colas, storage y programador

- **Storage link**: `php artisan storage:link` (imágenes de proyectos y CV se sirven desde
  `storage/app/public`).
- **Colas**: el driver por defecto es `database`. Ejecuta un worker si quieres procesar las
  notificaciones/correo en segundo plano:

  ```bash
  php artisan queue:work
  ```

- **Programador de tareas** (cron), añade en producción:

  ```cron
  * * * * * cd /ruta/al/proyecto && php artisan schedule:run >> /dev/null 2>&1
  ```

## Documentación

- [`docs/ARCHITECTURE.md`](docs/ARCHITECTURE.md) — arquitectura técnica.
- [`docs/INFORMATION_ARCHITECTURE.md`](docs/INFORMATION_ARCHITECTURE.md) — rutas y navegación.
- [`docs/DESIGN_SYSTEM.md`](docs/DESIGN_SYSTEM.md) — tokens, tipografía, componentes.
- [`docs/CONTENT_MODEL.md`](docs/CONTENT_MODEL.md) — modelos y relaciones.
- [`docs/ADMIN_GUIDE.md`](docs/ADMIN_GUIDE.md) — guía del panel.
- [`docs/RESPONSIVE_QA.md`](docs/RESPONSIVE_QA.md) — checklist responsive + capturas.
- [`docs/DEPLOYMENT.md`](docs/DEPLOYMENT.md) — despliegue.
- [`docs/SECURITY.md`](docs/SECURITY.md) — seguridad.
- Capturas de QA: [`docs/previews/`](docs/previews/).

## Limitaciones conocidas

Ver la sección correspondiente en [`docs/ARCHITECTURE.md`](docs/ARCHITECTURE.md#limitaciones-conocidas).
