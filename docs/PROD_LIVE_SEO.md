# SEO tecnico de produccion

## Observado

- `robots.txt` produccion: permite rastreo y declara `Sitemap: https://masqueclima.es/sitemap.xml`.
- Sitemap produccion: `127` URLs, incluye `/` aunque `/` devuelve `403`.
- Sitemap repo/dev: `126` URLs, no incluye `/`.
- Canonical homes: cada home apunta a su ruta de idioma (`/es/`, `/en/`, `/de/`, `/nl/`, `/ru/`, `/no/`).
- `hreflang`: produccion tiene alternates para los seis idiomas.
- Bug: `x-default` del grupo home apunta a `https://masqueclima.es/`; debe apuntar a `https://masqueclima.es/es/`.
- Landings de zona: `x-default` apunta a la URL espanola equivalente, no a `/`.
- Reformas: canonical/hreflang existen en paginas vivas, pero las URLs no estan en sitemap.
- JSON-LD: homes no traen JSON-LD en la captura; reformas traen JSON-LD propio. En repo/dev se inyecta JSON-LD basico solo cuando falta.

## Estrategia aplicada en repo/dev

- `/` redirige `301` a `/es/`.
- `/es/` es la URL canonica espanola.
- `lang_url('es')` devuelve `/es/`, no `/`.
- `x-default` del grupo home se corrige a `/es/`.
- Canonical y hreflang permanecen absolutos con dominio `https://masqueclima.es`.
- `public/sitemap.xml` mantiene las `126` URLs canonicas del sitemap vivo y excluye `/`.
- Dev debe servir `X-Robots-Tag: noindex, nofollow, noarchive` y robots bloqueante desde Nginx.
