# Rollback futuro Nicalia

No ejecutar todavia.

## Material necesario

- Backup previo de `public_html`.
- Copia de `.htaccess`, `robots.txt`, `sitemap.xml` anteriores.
- Lista de archivos subidos en el redeploy.

## Rollback

1. Activar mantenimiento solo si hay 500/rotura grave.
2. Restaurar `.htaccess` anterior.
3. Restaurar archivos PHP/HTML/asset modificados desde backup.
4. Limpiar cache LiteSpeed/cPanel.
5. Validar `/`, `/es/`, idiomas, `/no/`, landings, reformas, formulario y assets.
6. Revisar logs de error.
