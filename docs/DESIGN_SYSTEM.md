# Sistema de diseño

Sistema sobrio, potente y técnico inspirado en arquitectura de software, flujos de datos e
infraestructura. Los tokens viven en `resources/css/app.css` como variables CSS y se consumen
con utilidades Tailwind (`var(--token)`), además de las clases de componente definidas en el
mismo archivo.

## Paleta (modo oscuro por defecto)

| Token | Valor |
| --- | --- |
| Fondo principal | `#07111F` |
| Superficie | `#0D1B2A` |
| Superficie elevada | `#122338` |
| Azul principal | `#3B82F6` |
| Azul luminoso | `#60A5FA` |
| Cian de apoyo | `#22D3EE` |
| Texto principal | `#F8FAFC` |
| Texto secundario | `#B8C4D4` |
| Bordes | `rgba(148, 163, 184, 0.18)` |
| Positivo | `#22C55E` · Advertencia `#F59E0B` · Error `#EF4444` |

Variante clara preparada: fondo `#F7F9FC`, superficie `#FFFFFF`, texto `#0F172A` / `#475569`,
bordes `#E2E8F0`.

## Tipografía

- **Encabezados**: Sora (self-hosted, con fallback del sistema).
- **Texto**: Inter.
- **Código / etiquetas técnicas / métricas**: JetBrains Mono.

Las fuentes se sirven localmente (vía `laravel-vite-plugin/fonts`) para no bloquear el render ni
depender de Google Fonts.

## Escala visual

- Bordes redondeados moderados (12–20 px).
- Sombras suaves.
- Espaciado generoso entre bloques (clase `.section`).
- Ancho máximo ~1280 px (`.container-page`).
- Jerarquía tipográfica clara y longitud de lectura cómoda.

## Componentes (clases utilitarias en `app.css`)

- `.container-page` — contenedor centrado con ancho máximo.
- `.section` — espaciado vertical de sección.
- `.eyebrow` — etiqueta superior en mayúsculas monoespaciada.
- `.card` / `.surface` — superficies con borde y sombra.
- `.btn` (+ `.btn-ghost`) — botones.
- `.chip` — etiquetas de tecnología/categoría.

Componentes Blade reutilizables en `resources/views/components/`: `layout`, `site.nav`,
`site.footer`, `site.system-map`, `site.project-card`.

## Movimiento

- Transiciones 150–350 ms; apariciones sutiles con pequeño desplazamiento.
- Microinteracciones en botones y tarjetas; el mapa de sistemas reacciona discretamente al
  cursor.
- **Progressive enhancement**: las animaciones de aparición (`[data-reveal]`) solo se aplican si
  JS está activo (`html.js`). Sin JS, el contenido es totalmente visible.
- Se respeta `prefers-reduced-motion`. No hay scroll hijacking.

## Mapa vivo de sistemas

Pieza visual diferenciadora en el hero. En escritorio: nodos + conexiones SVG con pulsos de
datos ligeros (HTML/CSS/SVG, sin Three.js). Tiene versión estática accesible y se convierte en
tarjetas verticales en móvil. Implementado en `components/site/system-map.blade.php`.

## Accesibilidad del sistema

Contraste suficiente, foco visible, estados no comunicados solo por color, SVG con títulos
cuando procede. Ver `RESPONSIVE_QA.md` y la sección de accesibilidad del brief.
