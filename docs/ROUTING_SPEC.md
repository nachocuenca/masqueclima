# Especificación de rutas

| Ruta | Descripción | Notas |
|------|-------------|-------|
| `/` | Landing en español | Canonical `https://dominio/` |
| `/{lang}/` | Landing en idioma (`es`, `en`, `de`, `nl`, `ru`) | Canonical/hreflang absolutos. `x-default` → `/es/` |
| `/assets/*` | Archivos estáticos (CSS/JS) | Cacheable 1 año |
| `/img/*` | Imágenes | Cacheable 1 año, `loading="lazy"` |
| `/video/*` | Vídeos | Cacheable 1 año, `preload="metadata"` |
| `/robots.txt` | Robots estático | `text/plain` |
| `/sitemap.xml` | Sitemap estático | `application/xml` |
| `?__health=1` | Healthcheck | Responde `OK` |
| `?__dbg=1` | Debug en entornos no producción | Imprime dump de routing |

## Ejemplos
- `/en` → redirige a `/en/`.
- `/es/contacto` → `404` (solo se sirve `/` en cada idioma).
- `/img/foto%20área.jpg` → correcto gracias a `asset()` con `rawurlencode`.
