# Plan futuro redeploy Nicalia

No ejecutar hasta aprobar staging.

## Antes de subir

1. Backup completo de `public_html`.
2. Backup de `.htaccess`, `robots.txt`, `sitemap.xml` y assets actuales.
3. Confirmar que dev pasa checklist completo.
4. Confirmar que `/no/`, landings y reformas siguen resueltas.
5. Preparar ventana de despliegue y rollback.

## Subida quirurgica

- Subir solo archivos legacy necesarios.
- No subir `.git`, `.next`, `node_modules`, Docker ni documentacion interna.
- Mantener `public_html/.htaccess` adaptado a cPanel/LiteSpeed.
- Subir `public/`, `app/`, `views/`, `vendor/` si PHPMailer/composer se usa.
- Configurar SMTP con host `masqueclima.es`, no `mail.masqueclima.es`.
- No eliminar assets existentes sin comprobacion de referencias.
- Limpiar cache LiteSpeed tras despliegue.

## Pruebas post subida

- `/` -> `301 /es/`.
- Idiomas y landings criticas `200`.
- `/no/` `200`.
- Reformas `200`.
- Popup/formulario y WhatsApp operativos.
- Canonical/hreflang/x-default revisados.
