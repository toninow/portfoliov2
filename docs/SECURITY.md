# Seguridad

## Medidas implementadas

- **CSRF**: protección en todos los formularios (middleware de Laravel).
- **Validación en servidor**: `ContactRequest` para el formulario público; validación en los
  formularios de Filament.
- **Autorización**: acceso al panel controlado por `User::canAccessPanel()` y roles
  (`super_admin`, `editor`). Rutas de `/admin` protegidas por el middleware de autenticación de
  Filament.
- **Rate limiting**: aplicado al envío del formulario de contacto para mitigar abuso.
- **Anti-bots**: honeypot en el formulario de contacto (además del rate limiting).
- **Mass assignment**: las rutas públicas no vinculan request → modelo directamente; los datos
  entran validados vía `ContactRequest` y los formularios del panel.
- **Escapado de contenido**: Blade escapa por defecto (`{{ }}`); no se usa `{!! !!}` con datos de
  usuario.
- **Subida de archivos**: validación de MIME real (JPEG/PNG/WebP/AVIF), límite de tamaño y
  nombres de archivo seguros generados por el framework/Filament.
- **Secretos**: solo en `.env`. `.env.example` no contiene credenciales reales. No hay
  credenciales en el repositorio ni en el contenido sembrado.
- **Previews de borrador**: la vista previa del panel no expone borradores públicamente; el
  contenido no publicado se filtra en las consultas públicas (scopes `published`).
- **HTTPS**: obligatorio en producción (ver `DEPLOYMENT.md`).

## Recomendaciones para producción

- **Contraseñas**: define `ADMIN_PASSWORD` fuerte y cambia la contraseña por defecto tras el
  primer acceso. Aplica una política de contraseñas robustas para los usuarios del panel.
- **MFA**: Filament permite añadir autenticación multifactor; habilítala para cuentas de admin.
- **Recuperación de contraseña**: configurar `MAIL_*` para el flujo de reset de Laravel.
- **Registro de actividad**: registrar acciones administrativas relevantes (auditoría). El CRM ya
  guarda `LeadActivity`; considerar un log de auditoría para cambios de contenido.
- **Copias de seguridad**: programar backups verificables de base de datos y de
  `storage/app/public` (imágenes, CV).
- **Cabeceras de seguridad**: configurar CSP, HSTS, `X-Content-Type-Options`, etc. a nivel de
  servidor web.
- **Actualizaciones**: mantener dependencias al día (`composer outdated`, `npm audit`).

## Datos sensibles y contenido

No publicar credenciales, tokens, direcciones privadas, rutas internas, nombres de clientes no
autorizados ni métricas empresariales no confirmadas. Los seeders y contenidos de ejemplo
respetan esta regla.
