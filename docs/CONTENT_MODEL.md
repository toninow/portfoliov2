# Modelo de contenido

## Decisión de traducciones

**Estrategia única: columnas JSON traducibles** con `spatie/laravel-translatable`. Cada modelo
con contenido bilingüe declara `public array $translatable = [...]` y almacena un objeto
`{"es": "...", "en": "..."}` por campo. Se eligió frente a tablas de traducción separadas por
simplicidad, menos joins y edición cómoda campo a campo. El sitio público lee el idioma activo
con `getTranslation($campo, $locale)`; el panel usa el helper `App\Filament\Support\Translatable`.

## Modelos

| Modelo | Propósito | Campos traducibles (JSON) | Notas |
| --- | --- | --- | --- |
| `User` | Usuarios del panel | — | `role` (`super_admin`/`editor`), `canAccessPanel()` |
| `SiteSetting` | Ajustes clave/valor del sitio | según clave | email, tiempo de respuesta, etc. |
| `Profile` | Perfil público | `bio`, titular, disponibilidad | `Profile::current()` |
| `HomepageSection` | Secciones editables de la home | `title`, `subtitle`, `body` | `key`, `is_visible`, `sort` |
| `Service` | Servicios | `title`, `summary`, `problems`, `includes`, `deliverables`, `use_cases` | slug, `is_published`, N:M con `Technology` |
| `SkillGroup` | Grupos de habilidades | `name` | `hasMany` skills |
| `Skill` | Habilidad | — | pertenece a grupo |
| `Technology` | Tecnología | — | agrupada por área; N:M con proyectos/servicios |
| `ProjectCategory` | Categoría de proyecto | `name`, `description` | slug único |
| `Project` | Proyecto / caso de estudio | `name`, `summary`, `description`, `problem`, `context`, `constraints`, `solution`, `process`, `decisions`, `result`, `improvements`, `role` | slug, `status`, `visibility`, `is_featured`, `featured_size`, `sort`, SEO, soft deletes |
| `ProjectImage` | Galería de proyecto | `alt`, `caption` | orden, tipo |
| `ProjectMetric` | Métrica de proyecto | `name`, `description` | valor/unidad, publicable |
| `Experience` | Experiencia | `role`, `description` | empresa, fechas, `is_current` |
| `Education` | Formación | `title`, `description` | institución, años |
| `Certification` | Certificación | `name` | emisor, fecha, URL |
| `SocialLink` | Redes sociales | — | plataforma, URL, orden |
| `NavigationItem` | Elementos de navegación | `label` | destino, orden |
| `MediaAsset` | Archivos de la galería | `alt`, `caption` | ruta, tipo MIME |
| `Lead` | Contacto/oportunidad (CRM) | — | ver estados abajo |
| `LeadActivity` | Actividad del lead | — | tipo, descripción |
| `Task` | Tarea (CRM) | — | vencimiento, estado |

## Estados de Lead (CRM)

`new` · `contacted` · `conversation` · `proposal_sent` · `won` · `lost` · `archived`.

Campos: `name`, `email`, `phone`, `company`, `country`, `subject`, `message`, `source`,
`status`, `estimated_value`, `assigned_to`, `next_follow_up_at`, `contacted_at`, `closed_at`,
`notes`, timestamps.

Actividades: lead creado, nota añadida, email registrado, llamada registrada, estado
modificado, seguimiento programado, tarea completada.

## Convenciones

- **Slugs únicos** con `spatie/laravel-sluggable` (`Project`, `Service`, `ProjectCategory`).
- **Soft deletes** solo donde tiene sentido (p. ej. `Project`).
- **Scopes** de conveniencia (`published`, orden por `sort`).
- **Casts** para booleanos, fechas y JSON.
- **Mass assignment**: los modelos usan `$guarded = []` con formularios validados en Filament y
  en `ContactRequest`; no hay binding directo de request → modelo en rutas públicas.

## Seed / migración de contenido

`database/seeders/PortfolioSeeder.php` inventaría y migra el contenido aprovechable de
`legacy-portfolio/` (perfil, tecnologías, proyectos históricos, redes, CV) y crea los proyectos
destacados y servicios editables descritos en el brief. Los proyectos antiguos se marcan como
"Proyectos anteriores". No se inventan métricas: los campos sin dato público real quedan vacíos
y editables.
