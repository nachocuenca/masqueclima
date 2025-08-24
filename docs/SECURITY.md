# Seguridad básica

## Configuración PHP
- `display_errors = Off` en producción.
- `error_log` personalizado dentro de `storage/error.log`.
- `expose_php = Off`.

## Formulario de contacto
- Honeypot (`company` oculto) ya implementado.
- Validar campos servidor-side y sanitizar con `trim` y `e()` al imprimir.
- Limitar tamaño máximo de mensaje (`strlen < 2000`).

## CSRF
- Si se usa `csrf_field()` agregar verificación `csrf_check()` (ya incluido condicionadamente).

## Otros
- Forzar HTTPS a nivel de panel (redirección 301).
- Revisar permisos `644` para archivos y `755` para directorios.
