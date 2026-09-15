# Deploy — NO USAR DESDE `main`

La rama `main` no representa producción y no debe desplegarse.

Procedimiento vigente:

1. Cambiar a la rama `production`.
2. Leer `DEPLOY.md` y `docs/DEPLOY.md` en esa rama.
3. Desplegar en el VPS `51.254.128.162` mediante releases inmutables bajo `/srv/apps/masqueclima/releases/` y switch del symlink `current`.
4. Recargar `php8.4-fpm` tras el switch y ejecutar QA.

Nicalia/cPanel se mantiene para correo/cPanel y no es el origen web público.

Rama correcta: https://github.com/nachocuenca/masqueclima/tree/production
