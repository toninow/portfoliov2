# Guía del panel de administración

Acceso en `/admin`. Autenticación con email + contraseña. Roles: `super_admin` y `editor`
(`User::canAccessPanel()`).

## Navegación (grupos)

- **Panel**: Escritorio (dashboard) · Vista previa del sitio.
- **Contenido**: Proyectos · Categorías · Servicios · Tecnologías · Habilidades ·
  Experiencia · Formación · Certificaciones · Secciones de inicio.
- **CRM**: Contactos (leads) · Tareas.
- **Configuración**: Redes sociales.

> Cobertura respecto al menú ideal del brief: los recursos anteriores cubren la gestión de todo
> el contenido público y del CRM básico. Algunos ítems del menú ideal (Perfil, Galería
> multimedia, Currículum, Idiomas, SEO global, Usuarios, Pipeline Kanban dedicado,
> Actividades/Seguimientos como vistas independientes) quedan como ampliación futura o se
> gestionan a través de los recursos/campos existentes. Ver `ARCHITECTURE.md` → Limitaciones.

## Dashboard

Widget de resumen (`StatsOverview`): leads nuevos, seguimientos próximos, tareas vencidas y
proyectos publicados / en borrador.

## Contenidos traducibles

Los formularios muestran y editan el idioma activo del panel (español). Al guardar, se conserva
la traducción del otro idioma. Esto se implementa con `App\Filament\Support\Translatable`
(`T::text` / `T::area`). Para editar el inglés, cambia el idioma de la aplicación o usa los
campos correspondientes según se amplíe la UI.

## Proyectos

- **Listado**: miniatura, nombre, categoría, estado (badge), destacado, año, orden, fecha de
  publicación. Filtros por estado, categoría y destacado + papelera (soft deletes).
- **Formulario tipo wizard** con pasos: Identidad · Presentación · Caso de estudio ·
  Tecnología · Multimedia · Publicación.
- **Estados**: borrador / publicado / archivado. **Visibilidad**: público / privado con resumen
  / borrador / archivado. **Destacado** con tamaño (grande/mediano/compacto) para la
  composición editorial de la home.
- **Imágenes**: validación de MIME real (JPEG/PNG/WebP/AVIF), límite de tamaño, nombres
  seguros y editor de imagen.

## Vista previa del sitio

Página `Vista previa del sitio` con marco de dispositivo (escritorio/tablet/móvil), selector de
página e idioma, refrescar y abrir en pestaña nueva. No expone borradores públicamente.

## CRM

- **Contactos (Leads)**: los envíos del formulario público crean un `Lead` con una
  `LeadActivity` inicial. Gestiona estado, seguimiento (`next_follow_up_at`), notas y valor
  estimado. El cambio de estado se hace con un selector (usable en móvil, sin arrastrar).
- **Tareas**: seguimiento de tareas con vencimiento.

## Buenas prácticas

- Confirmaciones en acciones destructivas.
- No publicar credenciales, tokens ni rutas internas en los contenidos.
- Rellenar métricas solo con datos públicos reales; dejar vacío si no existe.
