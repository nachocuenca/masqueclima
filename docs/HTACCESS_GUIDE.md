# Guía de `.htaccess`

## `public_html/.htaccess`
- Activa `RewriteEngine`.
- Deja pasar archivos reales (`-f`, `-d`).
- Mapea `/assets`, `/img`, `/video` a `masqueclima/public/`.
- Cualquier otra ruta cae en `masqueclima/public/index.php`.
- Usa un *negative lookahead* (`^(?!masqueclima/)`) para evitar bucles.

## `masqueclima/public/.htaccess`
- Define `DirectoryIndex index.php`.
- Fuerza `AddDefaultCharset utf-8`.
- Aplica cabeceras de caché a CSS/JS/imágenes/vídeo (1 año, `immutable`).
- Acepta rutas `/es|en|de|nl|ru/`.
- Todo lo demás se sirve desde `index.php`.

Ambos ficheros están incluidos en el repositorio y pueden copiarse directamente al servidor.
