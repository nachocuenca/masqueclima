# Troubleshooting

| Problema | Causa probable | Solución |
|----------|----------------|---------|
| 404 en assets | Falta mapeo en `public_html/.htaccess` | Verificar reglas `/assets`, `/img`, `/video` |
| Bucle de redirección | Regla catch‑all sin `negative lookahead` | Usar `^(?!masqueclima/)` en raíz |
| Error 500 | Sintaxis PHP o permisos | Consultar `error_log`, validar con `php -l` |
| CORS en recursos externos | CDN bloqueado | Asegurar URLs HTTPS y `crossorigin` en `preconnect` |
| Sitemap/robots mezclan PHP | Archivos dinámicos anteriores | Reemplazar por `public/sitemap.xml` y `public/robots.txt` estáticos |
