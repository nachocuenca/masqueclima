# Plan de Pull Request

## Archivos añadidos
- `docs/`*: auditoría, arquitectura, guías, checklists.
- `tools/deploy-verify.sh`
- `tools/lint-htaccess.sh`
- `tools/healthcheck.php`
- `public_html/.htaccess`
- `public/.htaccess`

## Resumen de cambios
1. Documentación exhaustiva para despliegue, SEO, i18n, rendimiento y seguridad.
2. Scripts de verificación y linting.
3. Reglas `.htaccess` definitivas con cache y routing.

## Orden recomendado
1. Revisar y aprobar documentación.
2. Testear scripts en staging.
3. Desplegar `.htaccess` y app en producción.
4. Ejecutar `tools/deploy-verify.sh`.

## Validación post-merge
- Confirmar que `/`, `/en/` y assets responden `200`.
- Revisar Search Console para canonical/hreflang.
