# Guía para agentes de IA (CLAUDE.md)

Contexto rápido para asistentes que trabajen en este repositorio.

## Qué es

Portafolio de Antonio Benalcázar convertido en aplicación Laravel administrable (CMS) con
CRM ligero. La web estática original se conserva en `legacy-portfolio/` **solo como referencia**;
no se sirve ni se debe modificar.

## Stack y convenciones

- Laravel 13, PHP 8.3+, Filament 4, Tailwind 4, Vite, PHPUnit, Pint.
- Nombres de clases/variables en **inglés**; textos públicos en **ES/EN**; admin en **español**.
- Contenido traducible: columnas JSON con `spatie/laravel-translatable` (propiedad
  `$translatable` en el modelo). Estrategia única y documentada en `docs/CONTENT_MODEL.md`.
- Slugs: `spatie/laravel-sluggable`.
- No añadir dependencias sin justificar. No usar React/Vue/Inertia/Bootstrap.

## Puntos clave del código

- Idiomas: middleware `App\Http\Middleware\SetLocale` (alias `setlocale`) + helper
  `App\Support\Locale`. Rutas en `routes/web.php` agrupadas por locale (`/` ES, `/en/...`).
- Datos globales de las vistas: `App\View\Composers\SiteComposer` (perfil, redes, ajustes).
- Formulario de contacto: `ContactController` → crea `Lead` + `LeadActivity`, honeypot,
  rate limiting, notifica al admin.
- Admin Filament en `app/Filament`. Panel: `app/Providers/Filament/AdminPanelProvider.php`.
- **Campos traducibles en Filament**: el plugin oficial aún no soporta Filament 4, por lo que se
  usa el helper `App\Filament\Support\Translatable` (`T::text()` / `T::area()`). Edita el idioma
  activo del admin y preserva el resto de traducciones. Úsalo en cualquier formulario nuevo con
  campos traducibles, y usa `->formatStateUsing(fn ($record) => $record->getTranslation('campo','es'))`
  en las columnas de tabla.

## Comandos frecuentes

```bash
php artisan migrate:fresh --seed   # reiniciar datos de desarrollo
php artisan test                   # tests
./vendor/bin/pint                  # formato
npm run build                      # compilar assets
bash scripts/capture.sh            # regenerar capturas (requiere server en :8123)
```

## Reglas

- No inventar datos personales, empresas, métricas ni certificaciones. Dejar campos vacíos
  editables cuando falte información real.
- No exponer credenciales/tokens/rutas internas.
- Verificar visualmente los cambios de UI (capturas en `docs/previews/`).
- Ejecutar tests + Pint + build antes de dar por terminado un cambio.
