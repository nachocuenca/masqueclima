# Auditoría técnica de masqueclima

## Resumen ejecutivo
La aplicación es una landing multilingüe en PHP sin framework con router propio. En local funciona correctamente, pero en entornos cPanel/Apache se han observado problemas de redirecciones, rutas de assets y control de caché. El código es compatible con PHP 8.2 y las dependencias son mínimas.

## Hallazgos y prioridades
### P0 – Bloqueantes
- **Bucles de redirección**: faltaban reglas de reescritura claras para separar `public_html` y la app en `masqueclima/public`.
- **Rutas de assets**: sin reglas de mapeo en raíz, los ficheros CSS/JS/imagen devolvían 404 en producción.
- **robots.txt/sitemap.xml dinámicos**: versiones previas generaban contenido vía PHP, lo que mezclaba encabezados y rompía caché.
- **Canonical y hreflang relativos**: se requieren URLs absolutas para SEO internacional.

### P1 – Recomendado
- **Headers de caché**: ausencia de políticas `Cache-Control` y `Expires` para `/assets`, imágenes y vídeo.
- **Codificación de caracteres**: no se forzaba UTF‑8 en `.htaccess`, provocando warnings en algunos hostings.
- **Orden de carga de scripts**: uso de `defer` general, pero sin control de dependencias externas (Swiper, Bootstrap) que podía bloquear.
- **Faltaba healthcheck**: monitorización manual dependía del router.

### P2 – Mejora
- **Documentación de despliegue**: inexistente o dispersa.
- **Plan de pruebas manual**: no había una lista cerrada de URLs críticas.
- **Guías SEO/i18n/accesibilidad**: faltaban pautas para contribuir sin romper consistencia.

## Conclusiones
Con la incorporación de reglas de reescritura robustas, archivos estáticos para robots/sitemap y documentación detallada, se espera reducir incidencias en producción y facilitar despliegues reproducibles.
