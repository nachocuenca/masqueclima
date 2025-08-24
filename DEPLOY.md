# Despliegue en cPanel

1. **Subir el repositorio** a `public_html/masqueclima/` usando Git o FTP.
2. En el `public_html/` raíz del dominio copiar:
   - `public_html/index.php`
   - `public_html/.htaccess`
3. En cPanel → *Select PHP Version* escoger **PHP 8.0+** para el dominio.
4. El front controller quedará en `public_html/masqueclima/public/index.php`.
5. Subir también `public/.htaccess`, `public/robots.txt` y `public/sitemap.xml` dentro de `masqueclima/public/`.
6. Limpiar cachés (cPanel o CDN) y comprobar que `/`, `/es/`… responden `200`.
