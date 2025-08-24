# Runbook operativo

## Reinicios
La app es estática y no mantiene procesos. Reinicios se limitan a:
- **Reiniciar PHP-FPM** desde cPanel si hay cambios de versión.
- **Vaciar caché** de LiteSpeed o del CDN cuando se actualicen assets.

## Logs
- `cPanel → Metrics → Errors`: últimas líneas del `error_log`.
- Para depuración manual se puede activar `?__dbg=1` en entornos no producción.

## Monitorización
- `?__health=1` responde `OK` y sirve para chequeos externos.
- Comprobar diariamente que no existan errores 500 o redirecciones en bucle.

## Seguridad básica
- `display_errors = Off` en producción y `error_log` dedicado.
- `expose_php = Off`.
- Formularios con campo honeypot y sanitización de entrada.
- Limitar mensajes de contacto a <2000 caracteres.
- Forzar HTTPS a nivel de panel y usar permisos `644/755`.

## Incidencias comunes
- **500**: revisar sintaxis PHP (`php -l`), permisos de archivos y `error_log`.
- **Bucles de redirección**: verificar reglas `.htaccess` y que el dominio apunte a `public_html/`.
- **404 de assets**: comprobar mapeo en `.htaccess` raíz y permisos de la carpeta `public/`.

## Backup y restauración
- Programar copias completas desde cPanel → *Backup*.
- Para restaurar, subir el paquete y reemplazar la carpeta `masqueclima` y `.htaccess`.
