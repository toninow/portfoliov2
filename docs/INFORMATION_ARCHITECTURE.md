# Arquitectura de información

## Navegación pública

Inicio · Proyectos · Servicios · Sobre mí · Contacto · Selector ES/EN · Botón "Hablemos" ·
Acceso al CV.

## Rutas públicas

Español en la raíz; inglés bajo `/en`. Nombres de ruta con prefijo `en.` para el inglés.

| Español | Inglés | Nombre | Controlador |
| --- | --- | --- | --- |
| `/` | `/en` | `home` / `en.home` | `HomeController` |
| `/proyectos` | `/en/proyectos` | `projects.index` | `ProjectController@index` |
| `/proyectos/{slug}` | `/en/proyectos/{slug}` | `projects.show` | `ProjectController@show` |
| `/servicios` | `/en/servicios` | `services.index` | `ServiceController@index` |
| `/sobre-mi` | `/en/sobre-mi` | `about` | `AboutController` |
| `/contacto` | `/en/contacto` | `contact` | `ContactController@index` |
| `/contacto` (POST) | `/en/contacto` (POST) | `contact.store` | `ContactController@store` |
| `/cv` | `/en/cv` | `cv` | `CvController` |

Utilitarias: `/sitemap.xml` (`sitemap`), `/robots.txt` (`robots`).
Solo en local: `/__dev-login` (autentica para QA del panel).

El cambio de idioma preserva la página actual mediante `App\Support\Locale::switchUrl()`.

## Página de inicio (orden de secciones)

1. Navegación (sticky, menú móvil accesible).
2. Hero (título, subtítulo, botones, disponibilidad editable) + **mapa vivo de sistemas**.
3. Banda de especialización.
4. Proyectos destacados (composición editorial: grande / medianos / compactos).
5. Capacidades (grid tipo bento).
6. Cómo trabajo (proceso de 5 pasos).
7. Experiencia y contexto.
8. Tecnologías agrupadas por área.
9. Sobre Antonio.
10. Llamada a la acción.
11. Contacto rápido.
12. Footer.

## Página de proyectos

Encabezado editorial, proyectos destacados, listado completo, búsqueda y filtros por categoría,
tecnología y año, estado vacío diseñado y URLs compartibles.

## Detalle de proyecto (caso de estudio)

Hero, categoría, título, resumen, rol, periodo, tecnologías, imagen principal, problema,
contexto, restricciones, solución, proceso, decisiones, resultado, métricas opcionales, galería,
próximas mejoras, navegación anterior/siguiente y CTA de contacto. Las secciones vacías se
omiten con elegancia.

## Otras páginas

- **Servicios**: 8 servicios editables con problemas que resuelve, qué incluye, entregables,
  casos de uso, tecnologías y CTA.
- **Sobre mí**: presentación, foto, trayectoria, experiencia, formación, certificaciones,
  tecnologías y CV.
- **Contacto**: introducción, formulario completo, redes, tiempo de respuesta y aviso de
  privacidad.
- **404**: página diseñada.

## Admin (`/admin`)

Ver `ADMIN_GUIDE.md` para el mapa de navegación del panel.
