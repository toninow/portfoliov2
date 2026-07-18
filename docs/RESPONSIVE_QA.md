# QA responsive y visual

## Breakpoints objetivo

| Dispositivo | Ancho |
| --- | --- |
| Escritorio grande | 1440 px |
| Portátil | 1280 px |
| Tablet horizontal | 1024 px |
| Tablet vertical | 768 px |
| Móvil grande | 430 px |
| Móvil estándar | 390 px |
| Móvil pequeño | 360 px |

## Capturas generadas

En `docs/previews/` (regenerables con `scripts/capture.sh` y el servidor en `:8123`):

| Archivo | Vista | Tamaño |
| --- | --- | --- |
| `home-desktop.png` | Inicio | 1440×1000 |
| `home-tablet.png` | Inicio | 768×1024 |
| `home-mobile-390.png` | Inicio | 390×844 |
| `home-mobile-360.png` | Inicio | 360×800 |
| `projects-desktop.png` | Proyectos | 1440×1000 |
| `projects-mobile.png` | Proyectos | 390×844 |
| `project-detail-desktop.png` | Detalle | 1440×1000 |
| `project-detail-mobile.png` | Detalle | 390×844 |
| `admin-login-desktop.png` / `-mobile.png` | Login | 1440×1000 / 390×844 |
| `admin-dashboard-desktop.png` / `-mobile.png` | Dashboard | 1440×1000 / 390×844 |
| `admin-project-edit-desktop.png` / `-mobile.png` | Wizard proyecto | 1440×1000 / 390×844 |
| `admin-site-preview.png` | Vista previa | 1440×1000 |

## Checklist de revisión visual

- [x] Sin scroll horizontal en móvil.
- [x] Título del hero legible sin cortes extraños.
- [x] Mapa de sistemas legible (y convertido a flujo vertical en móvil).
- [x] Botones sin superposición; tappables en móvil.
- [x] Menú móvil funcional (hamburguesa accesible).
- [x] Jerarquía de tarjetas mantenida (composición editorial).
- [x] Formularios cómodos; texto de formulario ≥ 16 px.
- [x] Tablas administrativas adaptadas / con columnas alternables.
- [x] Acciones importantes no dependientes solo de hover.
- [x] Contenido de secciones visible sin JS (`[data-reveal]` solo se oculta con `html.js`).

## Comprobaciones adicionales

- Zoom 200 %, navegación por teclado y foco visible.
- `prefers-reduced-motion` respetado.
- Modo oscuro por defecto (paleta principal).
- Textos largos ES/EN, imágenes ausentes y proyectos sin métricas manejados con elegancia.

## Notas

Se realizó una segunda pasada visual tras la primera implementación: se corrigió la visibilidad
del contenido sin JS en las páginas con animación de aparición y se ajustó el render de campos
traducibles en el panel (antes mostraban `[object Object]`).
