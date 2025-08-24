# Rendimiento y Core Web Vitals

## Imágenes y vídeo
- Todas las imágenes usan `loading="lazy"` y `decoding="async"` para mejorar LCP.
- Vídeo del hero: `preload="metadata"`, `autoplay` silencioso y `poster` estático.
- Optimizar tamaños: exportar JPEG/WEBP a 1920 px máximo.

## CSS/JS
- CSS propio en `assets/css/styles.css` minificado.
- Scripts externos (`Bootstrap`, `Swiper`) se cargan con `defer` desde CDN.
- Orden: primero librerías externas, luego `main.js`.

## Preconexiones
- `preconnect` a `fonts.googleapis.com` y `fonts.gstatic.com` para reducir TTFB de fuentes.

## Evitar CLS
- Definir `width`/`height` en imágenes y `video`.
- Navbar y hero con tamaños fijos en CSS para evitar saltos.

## Auditoría
- Pasar Lighthouse en cada idioma (`/?__dbg=1` desactivado).
- Objetivo: LCP < 2.5 s, CLS < 0.1, TBT < 300 ms.
