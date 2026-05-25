# SEO baseline actual antes de migrar

Fecha de auditoría: 2026-05-25.

## Alcance

Auditoría del repo local `masqueclima_repo` en rama `feature/next-vps-seo-rebuild`, más comprobaciones puntuales contra producción viva `https://masqueclima.es` para detectar riesgos de cutover.

## Arquitectura PHP local

- Aplicación PHP 8.2 sin framework.
- Entrada legacy principal: `public/index.php`.
- Bootstrap/config: `app/bootstrap.php`, `app/config.php`.
- Helpers SEO/rutas: `app/helpers.php`.
- Router alternativo legacy: `app/router.php`, pero `public/index.php` resuelve actualmente home/404.
- Vistas: `views/layout.php`, `views/home.php`, parciales `views/partials/header.php`, `footer.php`, `contact.php`.
- i18n: `app/translations/{es,en,de,nl,ru}.php`.
- Contenido no expuesto por el router local actual: `app/content/services`, `areas`, `guides`, `cases`.
- Assets críticos: `public/assets/css/styles.css`, `public/assets/js/main.js`, imágenes, logos, flags, vídeo hero y `og.jpg`.
- Dependencias PHP: PHPMailer en `vendor/`.

Los ficheros legacy que chocaban con rutas dinámicas Next se han movido a `legacy/public/`: `index.php`, `robots.txt`, `sitemap.xml`. Los assets siguen en `public/assets`.

## Idiomas locales

Idiomas configurados en `app/config.php`:

- `es`
- `en`
- `de`
- `nl`
- `ru`

El idioma por defecto local es `es`.

## Rutas locales reales

El `public/index.php` legacy sirve:

- `/`
- `/es/`
- `/en/`
- `/de/`
- `/nl/`
- `/ru/`

Cualquier segmento adicional acaba en 404 porque el router usado por `public/index.php` solo asigna `home` cuando no quedan segmentos tras quitar idioma.

Existen vistas y contenidos legacy para servicios/zonas/guías/casos, pero no están conectados de forma funcional en el router local auditado.

## Producción viva observada

Comprobaciones con `curl` el 2026-05-25:

- `https://masqueclima.es/` devuelve `403 Forbidden`. Es P0.
- `https://masqueclima.es/es/` devuelve `200 OK`.
- `https://masqueclima.es/no/` devuelve `200 OK`, aunque `no` no existe en el repo local ni está en el alcance de idiomas pedido.
- `https://masqueclima.es/sitemap.xml` contiene más URLs que el repo local, incluyendo landings tipo `/es/aire-acondicionado-benidorm/` y equivalentes `en/de/nl/ru/no`.

Esto indica deriva entre producción viva y repo local. Antes de cutover de producción hay que exportar el sitemap completo vivo y decidir importación o redirección 301 para cada URL.

`tools/compare-prod-vs-dev.mjs` también detecta diferencias de H1 entre producción viva y el contenido del repo local para `en/de/nl/ru`. Como las traducciones extranjeras no deben reescribirse libremente, esta diferencia queda bloqueada como revisión previa al cutover: importar el texto vivo o aprobar explícitamente el texto del repo.

## Titles, descriptions y H1 actuales

La plantilla PHP usa `config('seo.title')` y `config('seo.description')`, no las claves `seo` de cada traducción.

Home local por idioma:

| Idioma | Title actual PHP | H1 actual |
| --- | --- | --- |
| es | Instalación y Mantenimiento de Climatización \| +QUECLIMA | Instalación y mantenimiento profesional de climatización |
| en | Air Conditioning Installation & Maintenance \| +QUECLIMA | Professional HVAC installation and maintenance |
| de | Klimaanlagen: Installation & Wartung \| +QUECLIMA | Professionelle Klima- und Heizungsinstallation |
| nl | Airconditioning: Installatie & Onderhoud \| +QUECLIMA | Professionele airco- en verwarmingsinstallatie |
| ru | Климатические системы: монтаж и обслуживание \| +QUECLIMA | Профессиональный монтаж и сервис климат-систем |

La descripción española actual en PHP config:

`Expertos en aire acondicionado, calefacción y energía solar en Alicante. Instalación, mantenimiento y reparación con 3 años de garantía.`

## Canonical y hreflang actuales locales

`views/layout.php` imprime canonical con `lang_url($current_lang)`.

Problema local:

- `lang_url('es')` devuelve `https://masqueclima.es/`.
- `lang_url('en')` devuelve `https://masqueclima.es/en/`.
- `hreflang es` apunta a `/`.
- `x-default` apunta a `/`.
- El sitemap local lista `/es/`, no `/`.

Problema de producción viva:

- `/` devuelve 403.
- `/es/` tiene canonical correcto a `/es/`.
- `x-default` observado en `/es/` apunta a `/`, que hoy devuelve 403.
- Sitemap vivo incluye `/` y `/no/`, además de URLs no presentes en repo.

## Sitemap actual

Sitemap local legacy en `legacy/public/sitemap.xml` lista solo:

- `/es/`
- `/en/`
- `/de/`
- `/nl/`
- `/ru/`

Sitemap vivo contiene muchas más URLs y alternates `xhtml`, incluyendo idioma `no`. Hay que auditarlo completo antes de producción.

## Robots actual

Robots local legacy en `legacy/public/robots.txt`:

- `Allow: /`
- Sitemap a `https://masqueclima.es/sitemap.xml`

Robots vivo observado:

- `Disallow:` vacío.
- Sitemap a `https://masqueclima.es/sitemap.xml`

Para staging nuevo, robots debe estar cerrado.

## JSON-LD actual

`app/helpers.php` imprime:

- `Organization`
- `HVACBusiness`
- `WebSite`
- `BreadcrumbList`
- `FAQPage` solo en home

Campos principales:

- Nombre: `+QUECLIMA`
- Logo: `/assets/img/masqueclimalogo_.png`
- Teléfono: `+34 613 02 66 00`
- Área servida: `Provincia de Alicante, España`
- Idiomas disponibles: `es,en,de,nl,ru`
- Servicios: instalación aire acondicionado, mantenimiento climatización, energía solar

## Contenido preservado

Migrado mecánicamente desde PHP a JSON en:

- `content/i18n/*.json`
- `content/legacy/services.es.json`
- `content/legacy/areas.es.json`
- `content/legacy/guides.es.json`
- `content/legacy/cases.es.json`

Wrappers TypeScript:

- `content/i18n/*.ts`
- `content/services/es.ts`
- `content/areas/es.ts`
- `content/blog/es.ts`

## Dependencias externas legacy

- Google Fonts Montserrat.
- Bootstrap 5.3.3 CDN.
- Swiper 9 CDN.
- Google Maps iframe.
- Elfsight Google Reviews widget.
- Google Analytics GA4 si hay `GA4_ID`.
- WhatsApp `https://wa.me/34613026600`.

La nueva Next elimina Bootstrap/Swiper/Google Fonts CDN. Mantiene Google Maps y, temporalmente, Elfsight como fallback hasta integrar Google Business Profile API.

## Problemas detectados

- P0: `/` en producción devuelve 403.
- P0: `x-default` vivo apunta a `/`, que devuelve 403.
- P0: producción viva tiene más URLs que el repo local.
- P1: idioma `no` existe en producción viva pero no está en el alcance ni repo local.
- P1: sitemap local y canonical local no coinciden para español.
- P1: las vistas legacy de servicios/áreas/guías parecen incompletas o no conectadas.
- P1: footer legacy pide claves `footer.tagline` y `footer.service`, pero traducciones actuales tienen `line2/line3`.
- P2: dependencias CDN y widget externo para reseñas.
