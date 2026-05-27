# Revision contenido reformas

Produccion tiene reformas vivo y enlazado, pero no aparece en el sitemap vivo. Por alcance aprobado: preservar en dev para auditoria, no anadir al sitemap y no redirigir todavia.

| Ruta | Status prod | Title | H1 | Canonical | x-default | JSON-LD |
| --- | ---: | --- | --- | --- | --- | ---: |
| `/es/reformas-integrales-benidorm/` | `200` | Instalación y Mantenimiento de Climatización | +QUECLIMA | Reformas integrales en Benidorm — llave en mano | `https://masqueclima.es/es/reformas-integrales-benidorm/` | `https://masqueclima.es/es/reformas-integrales-benidorm/` | `4` |
| `/en/benidorm-renovations/` | `200` | Air Conditioning Installation & Maintenance | +QUECLIMA | Home renovations in Benidorm — turnkey | `https://masqueclima.es/en/benidorm-renovations/` | `https://masqueclima.es/es/reformas-integrales-benidorm/` | `4` |
| `/de/renovierungen-benidorm/` | `200` | Klimaanlagen: Installation & Wartung | +QUECLIMA | Komplettsanierung in Benidorm — schlüsselfertig | `https://masqueclima.es/de/renovierungen-benidorm/` | `https://masqueclima.es/es/reformas-integrales-benidorm/` | `4` |
| `/nl/verbouwingen-benidorm/` | `200` | Airconditioning: Installatie & Onderhoud | +QUECLIMA | Renovatie in Benidorm — turnkey | `https://masqueclima.es/nl/verbouwingen-benidorm/` | `https://masqueclima.es/es/reformas-integrales-benidorm/` | `4` |
| `/ru/remont-benidorm/` | `200` | Климатические системы: монтаж и обслуживание | +QUECLIMA | Ремонт в Бенидорме — под ключ | `https://masqueclima.es/ru/remont-benidorm/` | `https://masqueclima.es/es/reformas-integrales-benidorm/` | `4` |
| `/no/oppussing-benidorm/` | `200` | Klimaanlegg og varmepumper – montering & service | +QUECLIMA | Totalrenovering i Benidorm — nøkkelferdig | `https://masqueclima.es/no/oppussing-benidorm/` | `https://masqueclima.es/es/reformas-integrales-benidorm/` | `4` |

## Decision actual

- Preservar reformas con `200` en dev.
- No anadir reformas a `public/sitemap.xml` mientras produccion no lo tenga ahi.
- No eliminar ni redirigir sin mapa y aprobacion posterior.

## Recomendacion para fase posterior

Valorar con datos SEO/comerciales si reformas debe mantenerse, noindexarse o redirigirse a climatizacion/servicio relevante. Cualquier cambio debe ir con mapa 301 y validacion de enlaces internos.
