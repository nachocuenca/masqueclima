# Estrategia canonical, hreflang y sitemap

## Producción futura

- Base URL: `https://masqueclima.es`.
- URL española canónica: `https://masqueclima.es/es/`.
- `/` debe redirigir 301 a `/es/`.
- `hreflang es`: `/es/`.
- `hreflang en`: `/en/`.
- `hreflang de`: `/de/`.
- `hreflang nl`: `/nl/`.
- `hreflang ru`: `/ru/`.
- `x-default`: `/es/`.

## Staging

- Base URL: `https://dev.masqueclima.es`.
- `robots.txt`: `User-agent: *` + `Disallow: /`.
- Todas las respuestas: `X-Robots-Tag: noindex, nofollow, noarchive`.
- Metadata robots: noindex/nofollow.
- Sitemap sin URLs.

## Páginas nuevas españolas

Las rutas de servicios, zonas, blog y contacto nacen solo en español. No emiten `hreflang` porque no hay traducciones equivalentes reales.

## Sitemap

Producción lista:

- homes `es/en/de/nl/ru`;
- páginas españolas nuevas;
- blog español;
- contacto español.

Staging no lista URLs para evitar envío accidental.

## 404

Next usa `app/not-found.tsx` y devuelve 404 real. No se debe redirigir toda URL desconocida a home.
