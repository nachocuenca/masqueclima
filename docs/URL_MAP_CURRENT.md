# Mapa de URLs actual y objetivo

## Criterio de migración

- Producción actual no se toca.
- Staging nuevo vive en `https://dev.masqueclima.es`.
- URL canónica española futura: `/es/`.
- `/` debe redirigir permanente a `/es/`.
- Sitemap Next debe listar solo URLs canónicas reales.
- Páginas nuevas españolas no generan `hreflang` si no hay equivalente real.

## Homes preservadas

| Actual | Nueva | Acción |
| --- | --- | --- |
| `/` | `/es/` | 301 en producción final. Corrige 403 actual. |
| `/es/` | `/es/` | Mantener 200 canonical. |
| `/en/` | `/en/` | Mantener 200 canonical. |
| `/de/` | `/de/` | Mantener 200 canonical. |
| `/nl/` | `/nl/` | Mantener 200 canonical. |
| `/ru/` | `/ru/` | Mantener 200 canonical. |

## Nuevas rutas españolas creadas

| Nueva URL | Fuente de contenido |
| --- | --- |
| `/es/servicios/aire-acondicionado/` | `app/content/services/es.php` slug `instalacion-aire-acondicionado` |
| `/es/servicios/mantenimiento-climatizacion/` | `app/content/services/es.php` |
| `/es/servicios/reparacion-aire-acondicionado/` | `app/content/services/es.php` slug `reparacion-averias` |
| `/es/servicios/calefaccion-bomba-calor/` | `app/content/services/es.php` slug `calefaccion-bomba-de-calor` |
| `/es/servicios/energia-solar/` | FAQ/home actual sobre solar térmica |
| `/es/zonas/benidorm/` | `app/content/areas/es.php` |
| `/es/zonas/finestrat/` | `app/content/areas/es.php` |
| `/es/zonas/la-nucia/` | `app/content/areas/es.php` |
| `/es/zonas/altea/` | `app/content/areas/es.php` |
| `/es/zonas/villajoyosa/` | `app/content/areas/es.php` |
| `/es/zonas/alfaz-del-pi/` | `app/content/areas/es.php` |
| `/es/zonas/calpe/` | `app/content/areas/es.php` |
| `/es/zonas/marina-baixa/` | Texto de cobertura actual |
| `/es/zonas/alicante/` | Texto de cobertura actual |
| `/es/blog/` | Guías legacy como blog |
| `/es/blog/[slug]/` | `app/content/guides/es.php` |
| `/es/contacto/` | Bloque de contacto actual |

## Redirecciones implementadas en `middleware.ts`

| Origen | Destino |
| --- | --- |
| `/` | `/es/` |
| `/es/servicios/instalacion-aire-acondicionado/` | `/es/servicios/aire-acondicionado/` |
| `/es/servicios/reparacion-averias/` | `/es/servicios/reparacion-aire-acondicionado/` |
| `/es/servicios/calefaccion-bomba-de-calor/` | `/es/servicios/calefaccion-bomba-calor/` |
| `/es/aire-acondicionado-benidorm/` | `/es/zonas/benidorm/` |
| `/es/aire-acondicionado-finestrat/` | `/es/zonas/finestrat/` |
| `/es/aire-acondicionado-la-nucia/` | `/es/zonas/la-nucia/` |
| `/es/aire-acondicionado-altea/` | `/es/zonas/altea/` |
| `/es/aire-acondicionado-villajoyosa/` | `/es/zonas/villajoyosa/` |
| `/es/aire-acondicionado-alfaz-del-pi/` | `/es/zonas/alfaz-del-pi/` |
| `/es/aire-acondicionado-calpe/` | `/es/zonas/calpe/` |

## Pendiente crítico antes de producción

El sitemap vivo contiene más URLs que este repo local. Antes del cutover hay que exportar todas las URLs de `https://masqueclima.es/sitemap.xml`, agruparlas por intención y completar una tabla definitiva:

- conservar con contenido equivalente;
- redirigir 301 a una página equivalente;
- mantener temporalmente;
- retirar solo si no tiene valor y con decisión explícita.

No hacer cutover si hay URLs vivas sin decisión.
