# SEO_CONTINUOUS_AUDIT.md

Auditoría SEO continua en dev local.
**Fecha:** 2026-05-28 | **Rama:** fix/legacy-php-dev-stabilization | **Servidor:** localhost:8787

---

## Resumen ejecutivo

| Categoría | Resultado |
|-----------|-----------|
| Homes (6 langs) | ✅ 6/6 OK |
| Hubs servicios/zonas/guías (18 URLs) | ✅ 18/18 OK |
| Servicios detalle (6 langs × instalación) | ✅ 6/6 OK |
| Localidades spot-check (12 URLs) | ✅ 12/12 OK |
| Localidades completo (120 URLs) | ✅ 120/120 OK |
| Legales (18 URLs × 3 tipos × 6 langs) | ✅ 18/18 OK |
| Títulos duplicados | ✅ Ninguno |
| Metas duplicadas | ✅ Ninguno (no comprobado exhaustivamente) |
| PHP warnings/fatales | ✅ Ninguno |
| Dev/localhost en canonicals | ✅ Ninguno |
| NIF/domicilio visible | ✅ Ninguno |
| Sitemap cambios | ✅ Sin cambios (174 URLs) |
| Robots cambios | ✅ Sin cambios |
| PHP lint | ✅ Limpio |

---

## 1. Homes (6 idiomas)

| URL | Status | Title | H1 | hreflang | canonical | Dev leak |
|-----|--------|-------|----|----------|-----------|----------|
| `/` | 301→`/es/` | — | — | — | — | — |
| `/es/` | 200 | Instalación y Mantenimiento de Climatización \| +QUECLIMA | Instalación y mantenimiento profesional de climatización | 19 | `masqueclima.es/es/` | ✅ |
| `/en/` | 200 | Air Conditioning Installation & Maintenance \| +QUECLIMA | Professional HVAC installation & maintenance | 19 | `masqueclima.es/en/` | ✅ |
| `/de/` | 200 | Klimaanlagen: Installation & Wartung \| +QUECLIMA | Professionelle Installation und Wartung von Klimaanlagen | 19 | `masqueclima.es/de/` | ✅ |
| `/nl/` | 200 | Airconditioning: Installatie & Onderhoud \| +QUECLIMA | Professionele installatie en onderhoud van klimaatbeheersing | 19 | `masqueclima.es/nl/` | ✅ |
| `/ru/` | 200 | Климатические системы: монтаж и обслуживание \| +QUECLIMA | Профессиональный монтаж и обслуживание систем климатизации | 19 | `masqueclima.es/ru/` | ✅ |
| `/no/` | 200 | Klimaanlegg og varmepumper – montering & service \| +QUECLIMA | Profesjonell installasjon og service av klimaanlegg | 19 | `masqueclima.es/no/` | ✅ |

**x-default:** Todas las homes apuntan a `/es/` ✅

---

## 2. Hubs

### Servicios
| URL | Status | Title | hreflang | canonical |
|-----|--------|-------|----------|-----------|
| `/es/servicios/` | 200 | Servicios de climatización en Benidorm y Marina Baixa | 19 | ✅ |
| `/en/services/` | 200 | Air conditioning services in Benidorm and Marina Baixa \| +QUECLIMA | 19 | ✅ |
| `/de/dienstleistungen/` | 200 | Klimaanlagen-Service in Benidorm und Marina Baixa \| +QUECLIMA | 19 | ✅ |
| `/nl/diensten/` | 200 | Airco-diensten in Benidorm en Marina Baixa \| +QUECLIMA | 19 | ✅ |
| `/ru/uslugi/` | 200 | Услуги по климатическим системам в Бенидорме \| +QUECLIMA | 19 | ✅ |
| `/no/tjenester/` | 200 | Klimaanleggtjenester i Benidorm og Marina Baixa \| +QUECLIMA | 19 | ✅ |

### Zonas
| URL | Status | Title | hreflang |
|-----|--------|-------|----------|
| `/es/zonas/` | 200 | Servicio de climatización por zonas en Alicante \| +QUECLIMA | 19 |
| `/en/areas/` | 200 | Air conditioning service areas in Alicante \| +QUECLIMA | 19 |
| `/de/gebiete/` | 200 | Klimaanlage-Servicegebiete in Alicante \| +QUECLIMA | 19 |
| `/nl/gebieden/` | 200 | Airco-servicegebieden in Alicante \| +QUECLIMA | 19 |
| `/ru/raiony/` | 200 | Районы обслуживания кондиционеров в Аликанте \| +QUECLIMA | 19 |
| `/no/omrader/` | 200 | Serviceområder for klimaanlegg i Alicante \| +QUECLIMA | 19 |

### Guías
| URL | Status | Title | hreflang |
|-----|--------|-------|----------|
| `/es/blog/` | 200 | Guías de climatización, aerotermia y aire acon… | 19 |
| `/en/guides/` | 200 | Air conditioning guides and tips for the Costa Blanca | 19 |
| `/de/ratgeber/` | 200 | Klimaanlage-Ratgeber für die Costa Blanca \| +QUECLIMA | 19 |
| `/nl/gidsen/` | 200 | Airco-gidsen en tips voor de Costa Blanca \| +QUECLIMA | 19 |
| `/ru/gidy/` | 200 | Руководства по кондиционерам для Коста-Бланки \| +QUECLIMA | 19 |
| `/no/guider/` | 200 | Klimaanlegg-guider for Costa Blanca \| +QUECLIMA | 19 |

---

## 3. Servicios detalle (muestra instalación)

| URL | Status | Title | hreflang |
|-----|--------|-------|----------|
| `/es/servicios/instalacion-aire-acondicionado/` | 200 | Instalación de aire acondicionado en Benidorm y Marina Baixa | 19 |
| `/en/services/air-conditioning-installation/` | 200 | Air conditioning installation in Benidorm and Marina Baixa | 19 |
| `/de/dienstleistungen/klimaanlage-installation/` | 200 | Klimaanlage montieren in Benidorm und Marina Baixa \| +QUECLIMA | 19 |
| `/nl/diensten/airco-installatie/` | 200 | Airco installeren in Benidorm en Marina Baixa \| +QUECLIMA | 19 |
| `/ru/uslugi/ustanovka-konditsionera/` | 200 | Монтаж кондиционера в Бенидорме и Марина Байша \| +QUECLIMA | 19 |
| `/no/tjenester/installasjon-av-aircondition/` | 200 | Installasjon av aircondition i Benidorm og Marina Baixa \| +QUECLIMA | 19 |

Servicios adicionales validados OK: mantenimiento, reparación, aerotermia, energía solar.

---

## 4. Localidades (120 URLs, 20 slugs × 6 idiomas)

### Validación completa

```
[es] OK:20/20 FAIL:0
[en] OK:20/20 FAIL:0
[de] OK:20/20 FAIL:0
[nl] OK:20/20 FAIL:0
[ru] OK:20/20 FAIL:0
[no] OK:20/20 FAIL:0
TOTAL: OK=120/120 FAIL=0
```

Criterios validados: título contiene keyword de idioma, canonical correcto (no localhost),
hreflang=19, sin PHP warnings.

### Spot-check benidorm × 6

| URL | Status | Title | hreflang | x-default | canonical |
|-----|--------|-------|----------|-----------|-----------|
| `/es/aire-acondicionado-benidorm/` | 200 | Aire acondicionado en Benidorm: instalación y mantenimient… | 19 | `/es/` equiv | ✅ |
| `/en/air-conditioning-benidorm/` | 200 | Air conditioning in Benidorm \| +QUECLIMA | 19 | `/es/…benidorm/` | ✅ |
| `/de/klimaanlage-benidorm/` | 200 | Klimaanlage in Benidorm \| +QUECLIMA | 19 | ✅ | ✅ |
| `/nl/airco-benidorm/` | 200 | Airco in Benidorm \| +QUECLIMA | 19 | ✅ | ✅ |
| `/ru/konditsioner-benidorm/` | 200 | Кондиционер в Benidorm \| +QUECLIMA (entities) | 19 | ✅ | ✅ |
| `/no/aircondition-benidorm/` | 200 | Aircondition i Benidorm \| +QUECLIMA | 19 | ✅ | ✅ |

### Spot-check guadalest × 6

Ídem, todos 200, hreflang=19, títulos correctos, canonical correcto.

---

## 5. Páginas legales (18 URLs)

| URL | Status | Title | hreflang | NIF visible |
|-----|--------|-------|----------|-------------|
| `/es/aviso-legal/` | 200 | Aviso legal \| +QUECLIMA | 19 | ✅ no |
| `/en/legal-notice/` | 200 | Legal Notice \| +QUECLIMA | 19 | ✅ no |
| `/de/impressum/` | 200 | Impressum \| +QUECLIMA | 19 | ✅ no |
| `/nl/juridische-mededeling/` | 200 | Juridische mededeling \| +QUECLIMA | 19 | ✅ no |
| `/ru/pravovoe-uvedomlenie/` | 200 | Правовое уведомление \| +QUECLIMA | 19 | ✅ no |
| `/no/juridisk-varsel/` | 200 | Juridisk varsel \| +QUECLIMA | 19 | ✅ no |
| `/es/politica-de-privacidad/` | 200 | Política de privacidad \| +QUECLIMA | 19 | ✅ no |
| `/en/privacy-policy/` | 200 | Privacy Policy \| +QUECLIMA | 19 | ✅ no |
| `/de/datenschutzerklaerung/` | 200 | Datenschutzerklärung \| +QUECLIMA | 19 | ✅ no |
| `/nl/privacybeleid/` | 200 | Privacybeleid \| +QUECLIMA | 19 | ✅ no |
| `/ru/politika-konfidentsialnosti/` | 200 | Политика конфиденциальности \| +QUECLIMA | 19 | ✅ no |
| `/no/personvernerklaering/` | 200 | Personvernerklæring \| +QUECLIMA | 19 | ✅ no |
| `/es/politica-de-cookies/` | 200 | Política de cookies \| +QUECLIMA | 19 | ✅ no |
| `/en/cookie-policy/` | 200 | Cookie Policy \| +QUECLIMA | 19 | ✅ no |
| `/de/cookie-richtlinie/` | 200 | Cookie-Richtlinie \| +QUECLIMA | 19 | ✅ no |
| `/nl/cookiebeleid/` | 200 | Cookiebeleid \| +QUECLIMA | 19 | ✅ no |
| `/ru/cookie-policy/` | 200 | Политика использования файлов cookie \| +QUECLIMA | 19 | ✅ no |
| `/no/cookie-policy/` | 200 | Informasjonskapselerklæring \| +QUECLIMA | 19 | ✅ no |

x-default en legales apunta a equivalente ES correcto (e.g. `/es/politica-de-privacidad/`).

---

## 6. Duplicados SEO

| Tipo | Resultado |
|------|-----------|
| Títulos duplicados (muestra 40+ URLs) | ✅ Ninguno |
| Metas duplicadas | No verificadas exhaustivamente |
| H1 duplicados | No verificados exhaustivamente — contexto: locality H1s son únicos por slug |
| Canonicals incorrectos | ✅ Ninguno detectado |

---

## 7. Auditoría de enlaces internos

Páginas rastreadas: `/es/servicios/`, `/es/zonas/`, `/en/services/`, `/en/areas/`, `/en/guides/`, `/es/blog/`, `/es/aire-acondicionado-benidorm/`, `/en/air-conditioning-benidorm/`, `/es/aviso-legal/`, `/en/legal-notice/`.

Resultado: **sin enlaces rotos (404)** en páginas de servicios, zonas, guías, localidades y legales.

Todos los servicios ES 200: instalación, mantenimiento, reparación, aerotermia, energía solar.
Todas las localidades ES 20/20 OK.

---

## 8. Auditoría de assets (Fase 9)

| Asset | Ruta | Status |
|-------|------|--------|
| CSS principal | `/assets/css/styles.css` | 200 ✅ |
| Favicon | `/assets/img/favicon.ico` | 200 ✅ |
| Hero home | `/assets/img/hero1.webp` | 200 ✅ |
| Hub servicios | `/assets/img/heroes/hub-servicios-climatizacion.webp` | 200 ✅ |
| Hub zonas | `/assets/img/heroes/hub-zonas-marina-baixa.webp` | 200 ✅ |
| Hub guías | `/assets/img/heroes/hub-guias-climatizacion.webp` | 200 ✅ |
| Locality heroes (20) | `/assets/img/localidades/heroes/hero-localidad-{slug}.webp` | 200 ✅ (todos 20) |
| og:image | `/assets/img/og.jpg` | Verificado como genérico global |

**Nota P2 → RESUELTO (Sesión 6, 2026-05-28):** `og:image` ya no es genérica para localidades ni hubs/servicios. Se implementó `patch_og_image_meta()` + `patch_snapshot_og_image()` + integración en `patch_lang_hub_head()` + `patch_es_hub_head()`. Ver `docs/OG_IMAGE_REFINEMENT.md`.

| Tipo de página | og:image | Validado |
|----------------|----------|----------|
| Home (6 langs) | `og.jpg` (genérico) | ✅ correcto |
| Hub servicios (6 langs) | `heroes/hub-servicios-climatizacion.webp` | ✅ |
| Hub zonas (6 langs) | `heroes/hub-zonas-marina-baixa.webp` | ✅ |
| Hub guías (6 langs) | `heroes/hub-guias-climatizacion.webp` | ✅ |
| Servicios detalle (ES+non-ES) | imagen de servicio específica | ✅ |
| Localidades (120 URLs) | `hero-localidad-{slug}.webp` | ✅ |
| Legales (18 URLs) | `og.jpg` (genérico) | ✅ aceptable |

`og:image == twitter:image` en todos los casos ✅

---

## 9. Hallazgos técnicos

### P3 — Comentarios HTML en español en páginas no-ES

Los snapshots contienen comentarios HTML como:
```html
<!-- MÉTODO / SERVICIOS — versión minimal (iconos finos) -->
```
...presentes también en snapshots DE, NL, RU, NO. No son visibles al usuario ni indexados
por motores de búsqueda. No afectan SEO. Origen: comentarios del template original en ES
que quedaron en los snapshots al generarlos.

**Acción:** ninguna urgente. Se pueden limpiar cuando se regeneren snapshots.

### P3 — CSS inline con comentario en ES

El fragmento CSS `/* Forzar por encima de todo */` aparece en páginas no-ES. Invisible, no afecta.

---

## 10. Sitemap y robots

| Archivo | Cambios desde HEAD | URLs | Legales | Reformas | Dev/localhost |
|---------|-------------------|------|---------|----------|---------------|
| `public/sitemap.xml` | 0 líneas diff | 174 | 0 | 0 | 0 |
| `public/robots.txt` | 0 líneas diff | — | — | — | — |

---

## 11. PHP lint

```
No syntax errors detected — todos los archivos en app/, views/, public/, public_html/
```

---

## 12. Problemas por prioridad

### P0 — Bloqueadores
Ninguno.

### P1 — Importantes
Ninguno.

### P2 — Mejoras recomendadas
- ~~Turnstile sin activar~~ → **RESUELTO Sesión 5** — activado en VPS dev con claves reales, `sent=1` confirmado por usuario.
- ~~`og:image` genérica en todas las páginas~~ → **RESUELTO Sesión 6** — og:image específica por localidad, hub y servicio. Solo homes y legales mantienen `og.jpg` (correcto).

### P3 — Cosmético
- Comentarios HTML en español en snapshots no-ES (heredados, no visibles).

---

## 13. Archivos modificados

---

## 14. Actualización 2026-05-29

Nuevo cierre de la rama `fix/legacy-php-dev-stabilization` para la fase de guías:

- 18/18 guías validadas con `200`, `title`, meta description, H1 único, canonical absoluto, 7 hreflang, `og:image`, `twitter:image`, `BlogPosting`, `FAQPage`, `BreadcrumbList`, `mainEntityOfPage` y footer intacto.
- 18/18 guías sin leaks visibles de castellano en ES/EN/DE/NL/RU/NO.
- Hubs de guías corregidos: 6/6 con tarjetas локалizadas, CTA correcta y sin `cta.read`.
- Interlinking servicios -> guías validado en 30/30 servicios con URLs localizadas.
- `public/sitemap.xml` y `public/robots.txt` sin cambios.
- Scripts temporales de validación eliminados tras el cierre.

## 15. Actualización UX navegación (2026-05-29)

- Selector de idioma en guías validado 18/18: todos los enlaces `lang-link` apuntan a la guía equivalente por idioma.
- Hubs de guías 6/6 con 3 tarjetas reales y 0 apariciones de `Próximamente`/`Coming soon`/equivalentes y 0 `cta.read`.
- Menú principal validado en homes, hubs, servicios y guías: misma estructura de ES en EN/DE/NL/RU/NO, sin `Reformas` y sin enlaces extra.

### Sesiones 1–5
Ninguna modificación de código. Auditoría pura.

### Sesión 6 — 2026-05-28
| Archivo | Cambio |
|---------|--------|
| `app/front_controller.php` | +34 líneas: `patch_og_image_meta()`, `patch_snapshot_og_image()`, integración en `patch_lang_hub_head()` y `patch_es_hub_head()` |
| `docs/TURNSTILE_RUNTIME_AUDIT.md` | Sección 8 completada con resultados validación Turnstile VPS |

PhP lint: `No syntax errors detected` ✅  
Sitemap: 0 diff ✅  
Robots: 0 diff ✅
