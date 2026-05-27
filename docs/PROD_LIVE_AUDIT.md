# Auditoria produccion viva

Fecha: 2026-05-26
Fuente de verdad: `https://masqueclima.es` en Nicalia/cPanel. No se toco produccion durante esta auditoria.

## Resumen

- `/` devuelve `403` en produccion y no debe ser URL canonica.
- `/es/`, `/en/`, `/de/`, `/nl/`, `/ru/`, `/no/` devuelven `200`.
- El sitemap vivo contiene `127` URLs: `126` URLs canonicas reales mas la raiz `/` con 403.
- La copia legacy del repo conserva las `126` URLs canonicas y elimina `/` del sitemap.
- Produccion usa popup/modal con `POST /contact-submit.php` y WhatsApp `https://wa.me/34613026600`.
- Produccion tiene reformas vivas en seis idiomas, pero no aparecen en el sitemap.
- El bug SEO principal observado es `hreflang x-default` a `https://masqueclima.es/` en el grupo home; en repo se corrige a `https://masqueclima.es/es/`.

## URLs base auditadas

| URL | Status | Final URL |
| --- | ---: | --- |
| `/` | `403` | `https://masqueclima.es/` |
| `/es/` | `200` | `https://masqueclima.es/es/` |
| `/en/` | `200` | `https://masqueclima.es/en/` |
| `/de/` | `200` | `https://masqueclima.es/de/` |
| `/nl/` | `200` | `https://masqueclima.es/nl/` |
| `/ru/` | `200` | `https://masqueclima.es/ru/` |
| `/no/` | `200` | `https://masqueclima.es/no/` |
| `/robots.txt` | `200` | `https://masqueclima.es/robots.txt` |
| `/sitemap.xml` | `200` | `https://masqueclima.es/sitemap.xml` |
| `/es/reformas-integrales-benidorm/` | `200` | `https://masqueclima.es/es/reformas-integrales-benidorm/` |
| `/en/benidorm-renovations/` | `200` | `https://masqueclima.es/en/benidorm-renovations/` |
| `/de/renovierungen-benidorm/` | `200` | `https://masqueclima.es/de/renovierungen-benidorm/` |
| `/nl/verbouwingen-benidorm/` | `200` | `https://masqueclima.es/nl/verbouwingen-benidorm/` |
| `/ru/remont-benidorm/` | `200` | `https://masqueclima.es/ru/remont-benidorm/` |
| `/no/oppussing-benidorm/` | `200` | `https://masqueclima.es/no/oppussing-benidorm/` |
