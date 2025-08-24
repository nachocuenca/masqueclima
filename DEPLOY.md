# Despliegue en cPanel (Nicalia)

1. **Subir el repo** al servidor dentro de `public_html/masqueclima/`.
2. Copiar los archivos de `public_html/` de este repositorio al `public_html/` real:
   - `public_html/index.php`
   - `public_html/.htaccess`
3. Asegurarse de que el dominio apunte a `public_html` y que esté habilitado **PHP 8.0 o superior** en cPanel.
4. El front controller real quedará en `public_html/masqueclima/public/index.php`.
5. Vaciar cachés o CDN si existen para ver los cambios.
