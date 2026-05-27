# Auditoria enlaces dev

Fecha: 2026-05-27
Entorno: `https://dev.masqueclima.es`

## Incidencia

Algunos enlaces visibles de dev salian hacia `https://masqueclima.es`, por ejemplo `Metodo` desde `/es/` o desde una landing interna. En otros casos el parche previo convertia enlaces absolutos de produccion en `/#metodo`, lo que no conservaba el idioma actual.

## Causa

La web legacy en dev sirve snapshots HTML capturados de produccion. Esos snapshots contienen enlaces visibles absolutos a `https://masqueclima.es/...`.

El parche anterior solo hacia un reemplazo simple en el body:

- `https://masqueclima.es/...` -> `/...`

Eso preservaba algunos paths, pero dejaba mal los anchors de home:

- `https://masqueclima.es/#metodo` -> `/#metodo`
- `https://masqueclima.es/?setlang=es` -> `/?setlang=es`

## Regla aplicada

Se separan usos SEO y runtime:

- Canonical, hreflang, og:url y JSON-LD pueden seguir apuntando a `https://masqueclima.es`.
- La navegacion visible debe usar rutas relativas en el host actual.
- `lang_url()` devuelve rutas relativas por defecto.
- `seo_lang_url()` devuelve URLs absolutas canonicas.

## Correcciones visibles

- `https://masqueclima.es/#metodo` -> `/{lang}/#metodo`
- `https://masqueclima.es/#zona` -> `/{lang}/#zona`
- `https://masqueclima.es/#faq` -> `/{lang}/#faq`
- `https://masqueclima.es/?setlang=es` -> `/es/`
- `https://masqueclima.es/no/?setlang=no` -> `/no/`
- `https://masqueclima.es/es/aire-acondicionado-beniarda/` -> `/es/aire-acondicionado-beniarda/`
- `var HOME = "https://masqueclima.es/<lang>/"` -> `var HOME = "/<lang>/"`

## Usos legitimos de `https://masqueclima.es` en dev

- `<link rel="canonical">`
- `<link rel="alternate" hreflang>`
- `og:url`
- `og:image` y Twitter image
- JSON-LD
- `public/sitemap.xml`

## Validacion esperada

- Ningun `href` visible del body apunta a `https://masqueclima.es`.
- Ningun `href="/#..."` visible queda en body.
- Desde `/es/aire-acondicionado-beniarda/`, `Metodo` apunta a `/es/#metodo`.
- Desde `/no/aircondition-beniarda/`, `Metode` apunta a `/no/#metodo`.
- Selector de idiomas usa rutas relativas en el host actual.
