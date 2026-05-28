# SITEMAP & HREFLANG FINAL AUDIT
**Branch:** `fix/legacy-php-dev-stabilization`  
**HEAD:** `a6210fe fix: add cookie policy page`  
**Date:** 2026-05-28  
**Auditor:** Automated (Copilot) + manual spot-check

---

## Resumen ejecutivo

| Métrica | Valor |
|---|---|
| Sitemap anterior | 126 URLs |
| Sitemap nuevo | **174 URLs** |
| URLs añadidas | +48 |
| Validación | APTA |
| Hreflang | 100% OK |
| Canonical | 100% OK |
| Dev/localhost en sitemap | 0 |
| Reformas en sitemap | 0 |
| Duplicados | 0 |

---

## Inventario de URLs en el sitemap nuevo

### 1. Homes (6)
```
/es/  /en/  /de/  /nl/  /ru/  /no/
```
- priority 1.0, changefreq weekly
- Hreflang: es + en + de + nl + ru + no + x-default→/es/

### 2. Hubs (18)
Servicios, zonas/áreas y guías en 6 idiomas:

| Tipo | ES | EN | DE | NL | RU | NO |
|---|---|---|---|---|---|---|
| Servicios/hub | /es/servicios/ | /en/services/ | /de/dienstleistungen/ | /nl/diensten/ | /ru/uslugi/ | /no/tjenester/ |
| Zonas/áreas | /es/zonas/ | /en/areas/ | /de/gebiete/ | /nl/gebieden/ | /ru/raiony/ | /no/omrader/ |
| Guías/blog | /es/blog/ | /en/guides/ | /de/ratgeber/ | /nl/gidsen/ | /ru/gidy/ | /no/guider/ |

- priority 0.8, changefreq weekly
- Hreflang cruzado entre equivalentes

### 3. Páginas de servicio (30)
5 servicios × 6 idiomas:

| Servicio | ES | EN | DE | NL | RU | NO |
|---|---|---|---|---|---|---|
| Instalación | /es/servicios/instalacion-aire-acondicionado/ | /en/services/air-conditioning-installation/ | /de/dienstleistungen/klimaanlage-installation/ | /nl/diensten/airco-installatie/ | /ru/uslugi/ustanovka-konditsionera/ | /no/tjenester/installasjon-av-aircondition/ |
| Mantenimiento | /es/servicios/mantenimiento-climatizacion/ | /en/services/climate-control-maintenance/ | /de/dienstleistungen/klimaanlagen-wartung/ | /nl/diensten/klimaatbeheersing-onderhoud/ | /ru/uslugi/obsluzhivanie-konditsionera/ | /no/tjenester/vedlikehold-av-klimaanlegg/ |
| Reparación | /es/servicios/reparacion-aire-acondicionado/ | /en/services/air-conditioning-repair/ | /de/dienstleistungen/klimaanlage-reparatur/ | /nl/diensten/airco-reparatie/ | /ru/uslugi/remont-konditsionera/ | /no/tjenester/reparasjon-av-aircondition/ |
| Aerotermia | /es/servicios/aerotermia-bomba-calor/ | /en/services/heat-pump-aerothermal/ | /de/dienstleistungen/waermepumpe-aerothermie/ | /nl/diensten/warmtepomp-aerothermie/ | /ru/uslugi/teplovoj-nasos/ | /no/tjenester/varmepumpe-aerotermi/ |
| Solar | /es/servicios/energia-solar-termica/ | /en/services/solar-thermal-energy/ | /de/dienstleistungen/solarthermie/ | /nl/diensten/zonneboiler-zonneenergie/ | /ru/uslugi/solnechnye-kollektory/ | /no/tjenester/solvarme/ |

- priority 0.75, changefreq monthly
- Hreflang cruzado entre equivalentes (6 langs + x-default→ES)

### 4. Landings locales (120)
20 localidades × 6 idiomas:

```
albir, alfaz-del-pi, altea, beniarda, benidorm, benifato,
benimantell, bolulla, callosa-den-sarria, calpe, confrides,
finestrat, guadalest, la-nucia, orxeta, polop, relleu,
sella, tarbena, villajoyosa
```

Patrones:
```
/es/aire-acondicionado-{localidad}/
/en/air-conditioning-{localidad}/
/de/klimaanlage-{localidad}/
/nl/airco-{localidad}/
/ru/konditsioner-{localidad}/
/no/aircondition-{localidad}/
```

- priority 0.85, changefreq weekly
- Hreflang cruzado entre equivalentes (6 langs + x-default→ES)
- Verificadas: 120/120 → 200 OK, canonical `https://masqueclima.es/{lang}/{prefix}-{localidad}/`

---

## URLs excluidas y motivo

| URL / Patrón | Motivo de exclusión |
|---|---|
| `https://masqueclima.es/` | Raíz redirige 301→/es/, no es URL canónica |
| `/politica-de-cookies` | Legal — criterio: no indexar legales en esta fase. Decisión futura pendiente. |
| `/es/politica-de-cookies/` | Ídem. Alias apunta a canonical sin /es/ prefijo. |
| `/es/reformas-integrales-benidorm/` | Reformas fuera de estrategia actual; contenido duplicado de home (title/meta=home); excluir de sitemap y navegación principal. P2 riesgo. |
| `/en/benidorm-renovations/` | Ídem (EN) |
| `/de/renovierungen-benidorm/` | Ídem (DE) |
| `/nl/verbouwingen-benidorm/` | Ídem (NL) |
| `/ru/remont-benidorm/` | Ídem (RU) |
| `/no/oppussing-benidorm/` | Ídem (NO) |
| Guías/artículos individuales | No existen snapshots ni rutas dinámicas individuales; solo hubs |
| URLs con parámetros, anchors | No aplican |
| `dev.masqueclima.es` | Nunca en producción |

---

## Estado hreflang/canonical

### Metodología de auditoría
- Crawl local completo contra `http://localhost:8787`
- PHP script + Invoke-WebRequest
- Fecha: 2026-05-28

### Resultados

| Tipo de página | Hreflang | Canonical | Dev URLs | x-default |
|---|---|---|---|---|
| Homes (6) | ✅ 6 langs + x-default | ✅ `masqueclima.es/xx/` | ✅ 0 | ✅ →/es/ |
| Hubs servicios (6) | ✅ 6 langs + x-default | ✅ correcto | ✅ 0 | ✅ →ES |
| Hubs zonas (6) | ✅ 6 langs + x-default | ✅ correcto | ✅ 0 | ✅ →ES |
| Hubs guías (6) | ✅ 6 langs + x-default | ✅ correcto | ✅ 0 | ✅ →ES |
| Servicios (30) | ✅ 6 langs + x-default | ✅ correcto | ✅ 0 | ✅ →ES |
| Landings locales (120) | ✅ 6 langs + x-default | ✅ correcto | ✅ 0 | ✅ →ES |

### Spot-check manual (4 páginas post-generación sitemap)
```
OK /en/services/air-conditioning-installation/ | status:200 | canon:✓ | hreflang:7
OK /de/dienstleistungen/klimaanlage-installation/ | status:200 | canon:✓ | hreflang:7
OK /es/aire-acondicionado-benidorm/ | status:200 | canon:✓ | hreflang:7
OK /no/aircondition-benidorm/ | status:200 | canon:✓ | hreflang:7
```

---

## Estado robots.txt

```
User-agent: *
Allow: /
Sitemap: https://masqueclima.es/sitemap.xml
```

- No modificado. ✅
- Sin `Disallow` accidental. ✅
- Referencia correcta al sitemap de producción. ✅
- En dev, el bloqueo de indexación se hace por Nginx (`X-Robots-Tag: noindex`) y por config de Nginx `robots Disallow`. No depende de este robots.txt.

---

## Estado /politica-de-cookies

| URL | Status | Canonical | H1 | Noindex |
|---|---|---|---|---|
| `/politica-de-cookies` | 200 | `https://masqueclima.es/politica-de-cookies` | ✅ "Política de cookies" | — |
| `/es/politica-de-cookies/` | 200 | `https://masqueclima.es/politica-de-cookies` | ✅ "Política de cookies" | — |

- Ambas URLs sirven el mismo contenido con canonical sin `/es/` prefijo.
- Excluida del sitemap en esta fase.
- **Pendiente de decisión:** ¿incluir legales en sitemap? Recomendación: incluir `/politica-de-cookies` en sitemap solo si se decide indexar legales. No es urgente para Nicalia.
- `noindex` no presente porque `APP_ENV` por defecto es `production` (sin `APP_ENV` env var). Correcto para deploy.

---

## Estado reformas

- 6 páginas de reformas existen en snapshots.
- Todas devuelven 200 pero con **title y meta de la home** (duplicate content riesgo P2).
- Canonical apunta a su propia URL.
- Excluidas de sitemap ✅.
- Excluidas de navegación principal ✅.
- **Recomendación futura:** O bien añadir `noindex` en esas páginas, o redirigir a home si reformas está definitivamente descartado.

---

## Validaciones ejecutadas

| Validación | Resultado |
|---|---|
| PHP lint (`php -l`) en todos los archivos | ✅ Sin errores |
| `git diff --check` | ✅ Sin whitespace errors |
| Sitemap XML válido | ✅ |
| Sitemap total 174 URLs | ✅ |
| 0 URLs dev/localhost en sitemap | ✅ |
| 0 cookies en sitemap | ✅ |
| 0 reformas en sitemap | ✅ |
| 0 duplicados | ✅ |
| 0 URL raíz `/` en sitemap | ✅ |
| 15/15 spot-check URLs presentes | ✅ |
| 3/3 URLs prohibidas ausentes | ✅ |
| 120/120 landings locales → 200 OK | ✅ |
| Hreflang: 4 spot-checks → 7 tags cada uno | ✅ |
| Robots.txt sin cambios | ✅ |

---

## Pendientes (no bloqueantes para Nicalia)

| Prioridad | Pendiente |
|---|---|
| P2 | Reformas: añadir `noindex` o redirigir a home si se descarta definitivamente |
| P2 | Legales: decidir si `/politica-de-cookies` entra en sitemap |
| P3 | Guías/artículos individuales: cuando existan, añadir al sitemap con hreflang correspondiente |
| P3 | Validación visual completa en navegador antes de Nicalia |
| P3 | Revisión legal del banner de cookies (contenido, no técnica) |

---

## Archivos modificados en esta fase

```
public/sitemap.xml   (126 → 174 URLs)
docs/SITEMAP_HREFLANG_FINAL_AUDIT.md   (creado)
docs/NICALIA_RELEASE_PLAN.md           (creado)
docs/FINAL_PREDEPLOY_AUDIT.md          (actualizado)
docs/SEO_MASTER_AUDIT.md               (actualizado)
```
