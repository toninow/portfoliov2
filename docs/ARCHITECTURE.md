# Arquitectura

## Visión general

Aplicación Laravel monolítica que renderiza el sitio público con Blade + Tailwind y expone un
panel de administración con Filament en `/admin`. No hay SPA ni API separada: el frontend es
server-rendered con progressive enhancement (Alpine/JS mínimo para menú, nav sticky y
animaciones de aparición).

```
Navegador
  │
  ├── Rutas públicas (routes/web.php)         Blade + Tailwind (server-rendered)
  │     └── Controllers → Models (Eloquent)
  │
  └── /admin  Filament 4 (Livewire) ──────────► Resources → Models
                                                Widgets, Pages (SitePreview)
Base de datos (MySQL prod / SQLite dev)
Storage público (imágenes de proyectos, CV)
```

## Capas

- **Rutas**: `routes/web.php`. Grupos por idioma mediante el middleware `setlocale` (ES en la
  raíz, EN bajo `/en`). Rutas utilitarias: `sitemap.xml`, `robots.txt`, `/cv`, y `/__dev-login`
  (solo entorno local, para QA).
- **Middleware**: `App\Http\Middleware\SetLocale` detecta el idioma del segmento de URL y fija
  el locale de la app.
- **Controllers** (`app/Http/Controllers`): `HomeController`, `ProjectController`,
  `ServiceController`, `AboutController`, `ContactController`, `CvController`,
  `SitemapController`. Delgados; consultan modelos con eager loading y devuelven vistas.
- **Requests**: `ContactRequest` valida el formulario e incluye honeypot.
- **View Composers**: `SiteComposer` comparte `siteProfile`, redes sociales y ajustes con
  layouts, páginas y componentes.
- **Modelos** (`app/Models`): Eloquent con `HasTranslations` donde aplica, `HasSlug`, casts,
  scopes (`published`, etc.) y relaciones. Ver `CONTENT_MODEL.md`.
- **Admin**: `app/Filament` (Resources, Widgets, Pages). Panel configurado en
  `app/Providers/Filament/AdminPanelProvider.php`.
- **Frontend build**: Vite (`vite.config.js`), Tailwind 4 (`resources/css/app.css`), fuentes
  self-hosted (Sora, Inter, JetBrains Mono) vía `laravel-vite-plugin/fonts`.

## Estrategia de idiomas

Una sola estructura de datos con **columnas JSON traducibles** (`spatie/laravel-translatable`).
Cada modelo declara `public array $translatable = [...]`. Se evita duplicar filas/páginas por
idioma. Las URLs públicas se resuelven con `App\Support\Locale::route()`.

Decisión y detalle en `CONTENT_MODEL.md`.

## Traducciones en el panel

El plugin `filament/spatie-laravel-translatable-plugin` aún no publica versión compatible con
Filament 4. Como puente se usa `App\Filament\Support\Translatable`:

- `formatStateUsing`: muestra el valor del idioma activo del admin (ES por defecto).
- `dehydrateStateUsing`: al guardar, fusiona el valor con las traducciones existentes para no
  perder el otro idioma.

Es un puente ligero y aislado; puede sustituirse por el plugin oficial cuando exista para v4.

## Almacenamiento de imágenes

Las imágenes de proyectos y el CV se guardan en `storage/app/public` y se sirven vía
`php artisan storage:link`. Las subidas en Filament validan MIME real (JPEG/PNG/WebP/AVIF),
tienen límite de tamaño y generan nombres seguros.

## Limitaciones conocidas

- **Filament 5 / Livewire 4**: no disponibles como estable a la fecha; se usa Filament 4 /
  Livewire 3. Migrable en el futuro.
- **Plugin de traducciones Filament**: sustituido por el helper propio (ver arriba). El admin
  edita ES; el inglés de contenidos de admin se gestiona con el mismo helper por campo.
- **Recursos de admin del brief no implementados como recurso propio**: algunos ítems del menú
  ideal (Perfil, Galería multimedia, Currículum, Idiomas, SEO, Usuarios, Pipeline Kanban,
  Actividades/Seguimientos como vistas separadas) se cubren parcialmente con los recursos
  existentes o quedan pendientes. Ver `ADMIN_GUIDE.md`.
- **Lighthouse**: los objetivos (90/95/95/95) son objetivos de producción; no se han medido
  puntuaciones reales en este entorno y no se declaran como alcanzadas.
- **Métricas de proyectos**: se dejan vacías/editables salvo que exista un dato público real. No
  se inventan cifras.
