# CONTENT, INTERLINKING & SEO AUDIT

## Addendum 2026-06-07 - P1 multilingual guide interlinking

Scope: local EN/DE/NL/RU/NO adaptation for the 6 P1 guide batch, interlinking validation and multilingual sitemap closure. No deploy, production, DNS, Nginx, SMTP, Turnstile, `.env`, forms, robots, Search Console, API, iframe or review schema changes.

Implemented local state:
- 36 P1 guide detail URLs are available across ES/EN/DE/NL/RU/NO.
- Each P1 guide links to a localized main service, localized services hub, localized areas hub and at least two localized locality pages.
- Non-ES guide content links to localized routes rather than Spanish routes, except for unavoidable global assets/navigation outside guide content.
- The language selector and hreflang maps now use the same contextual guide equivalents.
- Google reviews remain before the final CTA; no `Review` or `AggregateRating` schema was added.
- `public/sitemap.xml` now includes the 36 P1 guide URLs with complete localized alternates and `x-default` to the ES equivalent.

Validation on `127.0.0.1:8787`:
- 36/36 P1 guide URLs returned 200 with self-canonical, one H1, `BlogPosting`, visible FAQ/`FAQPage`, Google reviews and final CTA.
- 36/36 emitted complete ES/EN/DE/NL/RU/NO hreflang and `x-default` to the ES equivalent.
- 133 localized internal links discovered from the 36 guides returned 200.
- 6/6 guide hubs returned 200 and showed the 6 P1 cards with no placeholders.
- Sitemap validation: XML OK, UTF-8 without BOM, 228 unique `<loc>` entries, 36 P1 URLs present and 7 alternates per P1 URL.
- `public/robots.txt` was checked and left unchanged.

Publication gate:
- Search Console remains pending until after deploy and production sitemap validation.

## Addendum 2026-06-07 - Google Reviews final polish

Scope: final local polish of the Google Reviews/social-proof module before deploy. No deploy, production, DNS, Nginx, SMTP, Turnstile, `.env`, forms, robots, sitemap, Search Console, API, iframe or review schema changes.

Current state:
- Commercial pages render the Google Reviews block before the final CTA.
- Legal pages do not render the Google Reviews block.
- The widget uses initials avatars only; no real user photos are used.
- The `con fotos` chip and visible updated-date line were removed.
- The Google CTA is now a softer integrated secondary action.
- The carousel keeps desktop/tablet/mobile behavior at 3/2/1 cards with smoother fade/slide transitions and `prefers-reduced-motion` support.
- No `Review` or `AggregateRating` schema was added for this module.

## Addendum 2026-06-07 - P1 ES sitemap prep

Scope: local sitemap preparation for the six prioritized Spanish guide URLs. No deploy, production, robots, DNS, Nginx, SMTP, Turnstile, `.env`, forms or Search Console changes.

Sitemap outcome:
- The six P1 ES guide URLs are now included in `public/sitemap.xml`.
- The six sitemap blocks remain `es_only`: no `xhtml:link` hreflang alternates and no `x-default` entries.
- `public/sitemap.xml` is clean UTF-8 without BOM after this pass.
- `public/robots.txt` was not edited.

Remaining publication tasks:
- Deploy only after the final release is approved.
- Validate deployed `/sitemap.xml`.
- Submit or inspect in Search Console only after deploy.

## Addendum 2026-06-07 - P1 ES guide interlinking implemented

Scope: implemented interlinking for the six prioritized Spanish guide URLs. No sitemap, robots, deploy or production changes.

Implemented guide URLs:
1. `/es/blog/cuanto-cuesta-instalar-aire-acondicionado-benidorm/`
2. `/es/blog/por-que-aire-acondicionado-no-enfria/`
3. `/es/blog/aire-acondicionado-conductos-o-split/`
4. `/es/blog/aire-acondicionado-apartamentos-turisticos-benidorm/`
5. `/es/blog/reparar-o-cambiar-aire-acondicionado/`
6. `/es/blog/como-ahorrar-luz-aire-acondicionado/`

Interlinking applied to each guide:
- At least one main related service detail URL.
- `/es/servicios/` service hub.
- At least two locality URLs.
- `/es/zonas/` zones hub.
- CTA/form path through the mid-page CTA and final CTA.
- Google reviews remain before the final CTA in the dynamic shell.

Local validation on `127.0.0.1:8787`:
- 6/6 new guide URLs return 200.
- 6/6 guide bodies are within the P1 target range, 901-1049 words.
- `/es/blog/` returns 200 and links to all six new P1 guides.
- Checked internal links from the six new guides return 200.
- The first three ES hub cards follow the requested priority order: cost in Benidorm, no-cooling diagnosis, conductos vs split.

Sitemap status:
- Closed in the sitemap prep addendum above: the six ES-only URLs are included in `public/sitemap.xml` without hreflang alternates.
- If multilingual versions are added later, update guide equivalence/hreflang maps only after every localized URL returns 200.

## Addendum 2026-06-07 - Blog SEO / guides expansion

Scope: historical editorial and interlinking planning for the next guide batch. Current implemented state is documented in the P1 ES guide interlinking addendum above.

Current guide interlinking audit:
- Existing ES guide details include 3 related service-detail links and 5 locality links each.
- Existing ES guide details do not explicitly include `/es/servicios/` or `/es/zonas/` in their guide-specific related link arrays.
- Dynamic shells already inject Google reviews before the final CTA, so the final conversion path exists.
- The guides hub cards are not showing summaries because `views/partials/guide_cards.php` checks `content[0]`, while guide records use `intro`.

New interlinking rule for every new or updated ES guide:
- Link to one main related service page.
- Link to at least 2 locality pages.
- Link to `/es/servicios/`.
- Link to `/es/zonas/`.
- Keep the final CTA/form path active.
- Keep Google reviews immediately before the final CTA.

Priority guides for implementation:
1. `/es/blog/cuanto-cuesta-instalar-aire-acondicionado-benidorm/`
2. `/es/blog/por-que-aire-acondicionado-no-enfria/`
3. `/es/blog/aire-acondicionado-conductos-o-split/`
4. `/es/blog/aire-acondicionado-apartamentos-turisticos-benidorm/`
5. `/es/blog/reparar-o-cambiar-aire-acondicionado/`
6. `/es/blog/como-ahorrar-luz-aire-acondicionado/`

Secondary ES guides and existing-guide updates:
- `que-potencia-aire-acondicionado-necesita-vivienda` (existing, update links/CTA)
- `mantenimiento-aire-acondicionado-antes-verano` (existing, update links/CTA)
- `aerotermia-bomba-calor-cuando-merece-la-pena` (existing, update links/CTA)
- `mejores-marcas-aire-acondicionado-costa-blanca`
- `errores-comprar-aire-acondicionado-online`
- `que-revisar-antes-instalar-aire-acondicionado-vivienda`

Sitemap gate:
- The six implemented P1 ES URLs were added in the sitemap prep pass above after local validation, as ES-only sitemap entries without alternates.
- Do not add the remaining planned ES URLs or any future EN/DE/NL/RU/NO equivalents to `public/sitemap.xml` until final URL approval, internal links, hreflang maps and every target URL are validated.
**Sesión 7 — fix/legacy-php-dev-stabilization**  
**Fecha:** 2026-05-29  
**Rama:** `fix/legacy-php-dev-stabilization` (HEAD pre-fix: `bfbc545`)  
**Alcance:** hubs × 6 langs, servicios × 5 × 6 langs, localidades × 20 × 6 langs, interlinking matrix, duplicados, castellano en no-ES.

---

## 7. Actualización 2026-05-29

- Guías: 18/18 URLs validadas con 200, canonical correcto, 7 hreflang, og/twitter image, BlogPosting, FAQPage, BreadcrumbList y `mainEntityOfPage`.
- Hubs de guías: 6/6 con tres tarjetas enlazando a guías reales, sin tarjetas de futuro y sin `cta.read`.
- Servicios: 30/30 páginas con un enlace localizado a la guía relacionada esperada, sin duplicados visibles y sin leaks visibles en no-ES.
- Temporales: los scripts de validación se eliminaron tras la comprobación.

Referencia de cierre: ver `docs/FINAL_SEO_PRODUCTION_READINESS_AUDIT.md` para el dictamen final de salida a dominio bueno.

## 8. Actualización 2026-05-30 — Social proof Google

- Nuevo bloque global de reseñas Google insertado justo antes del CTA final en plantillas comerciales.
- Arquitectura de datos manual (`app/content/google_reviews.php`) para evitar dependencia API en runtime.
- Datos iniciales cargados: rating 5,0, 70 opiniones y 12 reseñas reales aportadas por usuario, sin fotos de usuarios.
- Enlace de referencia aportado por usuario: `https://share.google/2YL5e0lFenNMWZNk1`.
- Sin cambios de schema reviews en JSON-LD (sin `Review`/`AggregateRating`).
- El bloque mantiene comportamiento seguro: si faltan datos base, no se renderiza.

## 1. RESUMEN EJECUTIVO

Se encontraron **2 bugs P1** de código y **5 hallazgos SEO P2/P3**. Los dos bugs P1 han sido corregidos y validados en esta sesión. No se realizaron commits, despliegues, ni cambios en producción.

| Severidad | Tipo | Estado |
|-----------|------|--------|
| P1 Bug    | zone_visual links ES en hubs no-ES | ✅ Corregido |
| P1 Bug    | guard p1-internal-links bloqueado por footer | ✅ Corregido |
| P2 SEO    | Locality pages sin links a servicios específicos | 📋 Documentado (sin fix automático) |
| P2 SEO    | Guide hubs con contenido muy fino (226-277 palabras) | 📋 Documentado |
| P3 SEO    | Riesgo leve de canibalización localidad vs. servicio | 📋 Aceptable / monitorizar |
| P3 SEO    | Non-ES locality pages sin nearby links en body | 📋 Documentado |
| P3 Info   | Meta titles no-ES locality más cortos que ES | 📋 Aceptable |

---

## 2. INVENTARIO DE URLS AUDITADAS

### Hubs (18 páginas, 6 idiomas × 3 tipos)

| Lang | Servicios | Zonas | Guías |
|------|-----------|-------|-------|
| ES | /es/servicios/ | /es/zonas/ | /es/blog/ |
| EN | /en/services/ | /en/areas/ | /en/guides/ |
| DE | /de/dienstleistungen/ | /de/gebiete/ | /de/ratgeber/ |
| NL | /nl/diensten/ | /nl/gebieden/ | /nl/gidsen/ |
| RU | /ru/uslugi/ | /ru/raiony/ | /ru/gidy/ |
| NO | /no/tjenester/ | /no/omrader/ | /no/guider/ |

### Servicios (30 páginas, 5 servicios × 6 idiomas)

**ES slugs:** instalacion-aire-acondicionado, mantenimiento-climatizacion, reparacion-aire-acondicionado, aerotermia-bomba-calor, energia-solar-termica  
**EN slugs:** air-conditioning-installation, climate-control-maintenance, air-conditioning-repair, heat-pump-aerothermal, solar-thermal-energy  
**DE slugs:** klimaanlage-installation, klimaanlage-wartung, klimaanlage-reparatur, aerothermie-warmepumpe, solarthermie  
**NL/RU/NO:** equivalentes localizados

### Localidades (120 páginas, 20 slugs × 6 idiomas)

**Slugs:** albir, alfaz-del-pi, altea, beniarda, benidorm, benifato, benimantell, bolulla, callosa-den-sarria, calpe, confrides, finestrat, guadalest, la-nucia, orxeta, polop, relleu, sella, tarbena, villajoyosa

---

## 3. MÉTRICAS DE CONTENIDO

### 3.1 Hubs

| Hub | Palabras | H2 | Links internos | CTAs |
|-----|----------|----|----------------|------|
| /es/servicios/ | 477 | 3 | 42 | 85 |
| /es/zonas/ | 395 | 3 | 65 | 87 |
| /es/blog/ | 393 | 2 | 25 | 85 |
| /en/services/ | 473 | 3 | 43 | 82 |
| /en/areas/ | 399 | 3 | 66* | 83 |
| /en/guides/ | 277 | 2 | 26 | 82 |
| /de/dienstleistungen/ | ~470 | 3 | 43 | 85 |
| /de/gebiete/ | ~395 | 3 | 40* | 85 |
| /de/ratgeber/ | 226 | 2 | 26 | 85 |
| /ru/gidy/ | 232 | 2 | 26 | 82 |
| /no/guider/ | 238 | 2 | 26 | 82 |

\* Corregido en esta sesión (antes incluían 20 links a páginas ES incorrectas)

**⚠️ P2: Guide hubs muy finos** — `/de/ratgeber/` (226 palabras), `/ru/gidy/` (232), `/no/guider/` (238), `/en/guides/` (277). Umbral recomendado para hubs listing: ≥300 palabras.

### 3.2 Páginas de Servicios (ES sample)

| Servicio | Palabras | H2 | CTAs | JSON-LD | Links zonas | Links localidades |
|----------|----------|----|------|---------|-------------|------------------|
| instalacion-aire-acondicionado | 559 | 6 | 26 | ✅ Service | 3 | 5 |
| mantenimiento-climatizacion | 530 | 6 | 26 | ✅ Service | 3 | 5 |
| reparacion-aire-acondicionado | 527 | 6 | 26 | ✅ Service | 3 | 5 |
| aerotermia-bomba-calor | 541 | 6 | 26 | ✅ Service | 3 | 5 |
| energia-solar-termica | 522 | 6 | 26 | ✅ Service | 3 | 5 |

**✅ Servicios: sólidos.** Todas las páginas superan 500 palabras, tienen 6 H2, 26 CTAs, JSON-LD `@type:Service` y links a zonas y localidades.

**⚠️ P3: Riesgo leve de canibalización** — El title de la localidad Benidorm ("Aire acondicionado en Benidorm: instalación y mantenimiento") tiene overlap semántico con el servicio de instalación ("Instalación de aire acondicionado en Benidorm y Marina Baixa"). Ambas páginas compiten por queries de "instalación AC Benidorm". Diferenciación: locality page es de cobertura geográfica; service page es de tipo de servicio. Riesgo bajo mientras canonicals sean correctas.

**✅ Canonicals servicio EN:** todas apuntan a `https://masqueclima.es/en/services/{slug}/` (confirmado).

### 3.3 Páginas de Localidades

| Localidad | Palabras (ES) | H2 | P1-links | Nearby (body) | Links servicios específicos |
|-----------|---------------|----|----------|---------------|---------------------------|
| benidorm | 842 | 13 | ✅ (post-fix) | 3 (Finestrat, La Nucía, Albir) | ❌ Solo hub |
| altea | 825 | 13 | ✅ (post-fix) | 3 (Albir, Calpe, La Nucía) | ❌ Solo hub |
| calpe | ~830 | ~13 | ✅ (post-fix) | 3 | ❌ Solo hub |
| finestrat | ~820 | ~13 | ✅ (post-fix) | 3 | ❌ Solo hub |
| la-nucia | ~830 | ~13 | ✅ (post-fix) | 3 | ❌ Solo hub |
| (15 P2) | ~810 | ~13 | ✅ (post-fix) | 3 | ❌ Solo hub |

**⚠️ P2: Links a servicios específicos ausentes** — Las páginas de localidad enlazan a `/es/servicios/` (hub) pero NO a servicios individuales (`/es/servicios/instalacion-aire-acondicionado/`, `/es/servicios/mantenimiento-climatizacion/`, etc.). Esto limita el flujo de PageRank hacia las service pages desde locality pages.

**Nota:** La sección `p1-internal-links` (post-fix Bug 2) ahora incluye: "Servicios de climatización" → `/es/servicios/`, "Todas las zonas" → `/es/zonas/`, y 3 localidades cercanas. Esto es mejor que el estado anterior (ningún block adicional), pero sigue sin links directos a servicios específicos.

---

## 4. BUGS P1 ENCONTRADOS Y CORREGIDOS

### Bug 1 — zone_visual sirve URLs ES en hubs de otros idiomas

**Archivo:** `app/front_controller.php`  
**Funciones afectadas:** `lang_hub_services_body()`, `lang_hub_areas_body()`  
**Impacto:** Las páginas `/en/areas/`, `/de/gebiete/`, `/nl/gebieden/`, `/ru/raiony/`, `/no/omrader/` y las equivalentes de servicios (`/en/services/`, `/de/dienstleistungen/`, etc.) contenían 20 links a páginas ES de localidades en el mapa SVG interactivo. Esto creaba una situación donde Googlebot rastreando `/en/areas/` encontraba 20 links a `/es/aire-acondicionado-{ciudad}/` pero ninguno a `/en/air-conditioning-{ciudad}/` desde el mapa (los pills correctos estaban separados).

**Fix aplicado:**
```php
// En lang_hub_services_body() y lang_hub_areas_body()
if ($lang !== 'es') {
    $langPrefix = match ($lang) {
        'en'    => '/en/air-conditioning-',
        'de'    => '/de/klimaanlage-',
        'nl'    => '/nl/airco-',
        'ru'    => '/ru/konditsioner-',
        'no'    => '/no/aircondition-',
        default => '/es/aire-acondicionado-',
    };
    $zoneMap = str_replace('href="/es/aire-acondicionado-', 'href="' . $langPrefix, $zoneMap);
}
```

**Validación:**
- `/en/areas/`: ES links = 0 (antes: 20), EN links = 40 ✅
- `/de/gebiete/`: ES links = 0 (antes: 20), DE links = 40 ✅

---

### Bug 2 — Guard de `insert_p1_internal_links` siempre bloqueado por footer

**Archivo:** `app/front_controller.php`  
**Función afectada:** `patch_es_p1_location_page()`  
**Línea:** ~2026

**Causa raíz:** La condición `!str_contains($html, '/es/servicios/')` siempre era `false` porque el footer del snapshot siempre contiene `/es/servicios/`. Esto impedía que la sección `p1-internal-links` (con nearby localities + CTAs enriquecidos) se insertara en ninguna de las 20 páginas de localidad ES.

**Fix aplicado:**
```php
// Antes:
if (!str_contains($html, '/es/servicios/')) {

// Después:
if (!str_contains($html, 'p1-internal-links')) {
```

**Validación:**
- `/es/aire-acondicionado-benidorm/`: `p1-internal-links` = 5 ocurrencias, `p1-pill` = 7 ✅
- `/es/aire-acondicionado-altea/`: `p1-internal-links` = 5 ocurrencias ✅
- Ahora todas las 20 páginas de localidad ES reciben el bloque de nearby + CTAs

---

## 5. AUDITORÍA SEO DETALLADA

### 5.1 Titles y Metas — Sin Duplicados

**Páginas de servicios ES:**
| Página | Title |
|--------|-------|
| instalacion | Instalación de aire acondicionado en Benidorm y Marina Baixa \| +QUECLIMA |
| mantenimiento | Mantenimiento de climatización en Benidorm y Marina Baixa \| +QUECLIMA |
| reparacion | Reparación de aire acondicionado en Benidorm y Marina Baixa \| +QUECLIMA |
| aerotermia | Aerotermia y bomba de calor en Benidorm y Marina Baixa \| +QUECLIMA |
| solar | Energía solar térmica en Benidorm y Marina Baixa \| +QUECLIMA |

✅ Todos únicos.

**Páginas de servicios EN:**
| Página | Title |
|--------|-------|
| installation | Air conditioning installation in Benidorm and Marina Baixa \| +QUECLIMA |
| maintenance | Air conditioning maintenance in Benidorm and Marina Baixa \| +QUECLIMA |
| repair | Air conditioning repair in Benidorm and Marina Baixa \| +QUECLIMA |
| heat-pump | Heat pump and aerothermal systems in Benidorm and Marina Baixa \| +QUECLIMA |
| solar | Solar thermal energy in Benidorm and Marina Baixa \| +QUECLIMA |

✅ Todos únicos.

**Páginas de localidades (muestra):**
| Página | Title |
|--------|-------|
| /es/aire-acondicionado-benidorm/ | Aire acondicionado en Benidorm: instalación y mantenimiento \| +QUECLIMA |
| /en/air-conditioning-benidorm/ | Air conditioning in Benidorm \| +QUECLIMA |
| /es/aire-acondicionado-altea/ | Aire acondicionado en Altea: instalación y mantenimiento \| +QUECLIMA |
| /en/air-conditioning-altea/ | Air conditioning in Altea \| +QUECLIMA |

✅ Distintos entre idiomas y entre ciudades.

**⚠️ P3 observación:** Los titles de locality pages EN son más cortos y menos descriptivos que los ES ("Air conditioning in Benidorm | +QUECLIMA" vs "Aire acondicionado en Benidorm: instalación y mantenimiento | +QUECLIMA"). Los ES tienen un subtítulo que mejora el CTR. Podría beneficiarse de enriquecer los EN con subtítulo descriptivo (fuera del scope actual).

### 5.2 H1 — Sin Duplicados entre páginas del mismo idioma

**✅ Servicios:** cada servicio tiene H1 único, diferenciado por tipo de servicio.  
**✅ Localidades:** cada localidad tiene su nombre propio en H1.  
**⚠️ P3:** H1 idéntico a title en todas las service pages (no tienen suffix de marca separado). Patrón usual aceptable.

### 5.3 Castellano visible en páginas no-ES

Se comprobaron los 15 hubs no-ES (EN/DE/NL/RU/NO × servicios/zonas/guías) buscando: presupuesto, Servicios, climatizacion, Zonas, Guias, Instalacion, Mantenimiento, nuestros, Reparacion, Solicita.

**Resultado: ✅ PASS — Ningún hub no-ES contiene castellano visible en el body.**

Nota: la corrección del Bug 1 también eliminó los 20 hrefs ES que aparecían en el SVG de zonas de los hubs no-ES.

### 5.4 Canonicals

**✅ Service pages:** todos con canonical correcto a `https://masqueclima.es/{lang}/...`  
**✅ Locality pages:** `patch_snapshot_seo_meta()` aplica canonical correcto en todas las rutas  
**✅ Hub pages:** `patch_lang_hub_head()` y `patch_es_hub_head()` inyectan canonical correcto

### 5.5 JSON-LD

**✅ Service pages:** `@type: Service` presente en las 5 páginas ES y EN verificadas  
**✅ Locality pages:** `snapshot_jsonld()` inyecta JSON-LD en snapshots sin schema existente  
**❓ Hubs:** no verificado explícitamente si tienen JSON-LD (`@type: WebPage` o `BreadcrumbList`). Consideración futura.

### 5.6 Canibalización de Keywords

**Riesgo identificado (P3):**
- Locality page Benidorm compite con service pages "instalación" y "mantenimiento" para queries como "instalación aire acondicionado Benidorm".
- **Diferenciación existente:** locality pages tienen `canonical` apuntando a su URL propia, service pages tienen canonicals distintos, y los titles/H1 son distintos.
- **Conclusión:** riesgo bajo. El interlinking post-fix (bug 2) hace que locality pages apunten a `/es/servicios/` (hub) pero no a service pages individuales — esto podría mejorar la señal de diferenciación si se añadieran links directos de localidad a servicios específicos.

---

## 6. MATRIX DE INTERLINKING

### 6.1 Estado por tipo de página

| Origen | → Home | → Servicios hub | → Servicio específico | → Zonas hub | → Localidad | → Blog/Guías |
|--------|--------|-----------------|----------------------|-------------|-------------|--------------|
| Hub Servicios ES | ✅ nav | ✅ self | N/A | ✅ pills | ✅ pills | ✅ nav |
| Hub Zonas ES | ✅ nav | ✅ pills | ❌ | ✅ self | ✅ map+pills | ✅ nav |
| Hub Guías ES | ✅ nav | ✅ nav | ❌ | ✅ nav | ❌ | ✅ self |
| Servicio individual ES | ✅ nav | ✅ breadcrumb | N/A | ✅ (3) | ✅ (5) | ✅ nav |
| Localidad ES (post-fix) | ✅ nav | ✅ p1-links | ❌ | ✅ p1-links | ✅ nearby×3 | ✅ nav |
| Hub Zonas EN (post-fix) | ✅ nav | ✅ pills | ❌ | ✅ self | ✅ map✓ +pills | ✅ nav |
| Localidad EN | ✅ nav | ✅ nav | ❌ | ✅ nav | ❌ | ✅ nav |

**Leyenda:** ✅ = link presente, ❌ = link ausente (gap identificado)

### 6.2 Gaps de interlinking más significativos

1. **Localidades ES → Servicios específicos (P2):** Las locality pages solo enlazan al hub `/es/servicios/` pero no a las 5 service pages individuales. Impacto: flujo de PageRank subóptimo desde localidades hacia servicios.

2. **Localidades no-ES → Nearby localities (P3):** Las páginas de localidad EN/DE/NL/RU/NO no tienen la sección `p1-internal-links` equivalente (esa función solo opera en `$lang === 'es'`). No hay nearby links en body content de localidades no-ES.

3. **Hub Zonas → Servicios específicos (P3):** Los hubs de zonas no enlazan a páginas de servicios individuales.

4. **Hub Guías → Localidades (P3):** Los hubs de guías no enlazan a páginas de localidades (relevante para guías como "cómo elegir AC en Benidorm").

---

## 7. CONTENIDO THIN — PRIORIZACIÓN

### P2: Guide hubs con contenido muy fino

| Hub | Palabras | Umbral recomendado | Acción |
|-----|----------|--------------------|--------|
| /de/ratgeber/ | 226 | ≥300 | Enriquecer intro + kicker |
| /ru/gidy/ | 232 | ≥300 | Enriquecer intro |
| /no/guider/ | 238 | ≥300 | Enriquecer intro |
| /en/guides/ | 277 | ≥300 | Enriquecer intro |
| /nl/gidsen/ | ~260 | ≥300 | Enriquecer intro |

Las guías existen como contenido (guide_simple, guide_calculator views), pero los hubs de guías no-ES tienen intros muy cortas. Los datos de contenido están en `app/content/hubs/{lang}.php`.

### P3: Body content similarity entre localidades

Las 20 páginas de localidad ES se sirven desde snapshots pre-generados con contenido altamente diferenciado (cada snapshot menciona características específicas de la localidad: salinidad en Benidorm, chalets en La Nucía, viñedos en Tàrbena, etc.). **No se detecta contenido thin o duplicado entre localidades**.

---

## 8. CORRECCIONES APLICADAS EN SESIÓN

### Archivos modificados

- **`app/front_controller.php`** — 2 fixes, 23 líneas añadidas, 1 línea modificada

### Detalle de cambios

```
app/front_controller.php | 24 +++++++++++++++++++++++-
 1 file changed, 23 insertions(+), 1 deletion(-)
```

### Validaciones post-fix

| Test | Resultado |
|------|-----------|
| /en/areas/ ES locality links = 0 | ✅ |
| /en/areas/ EN locality links = 40 | ✅ |
| /de/gebiete/ ES locality links = 0 | ✅ |
| /de/gebiete/ DE locality links = 40 | ✅ |
| /es/aire-acondicionado-benidorm/ p1-internal-links present | ✅ |
| /es/aire-acondicionado-altea/ p1-internal-links present | ✅ |
| PHP lint — 0 errores | ✅ |

---

## 9. HALLAZGOS SIN FIX (PENDIENTES FUTURA SESIÓN)

### P2 — Locality pages sin links a servicios específicos

**Descripción:** Las 20 páginas de localidad ES (y las 100 no-ES) enlazan a los hubs de servicios pero no a las 5 service pages individuales.

**Impacto SEO:** Flujo de PageRank subóptimo. Los crawlers no reciben señal directa de que "Benidorm" es relevante para "instalación AC" específicamente.

**Solución propuesta:** En `insert_p1_internal_links()`, añadir links a las 5 service pages:
```php
$block = <<<HTML
...
<div class="mb-2">
  <a class="p1-pill" href="/es/servicios/instalacion-aire-acondicionado/">Instalación AC</a>
  <a class="p1-pill" href="/es/servicios/mantenimiento-climatizacion/">Mantenimiento</a>
  <a class="p1-pill" href="/es/servicios/">Todos los servicios</a>
  <a class="p1-pill" href="/es/zonas/">Todas las zonas</a>
  {$nearby}
</div>
```

### P2 — Guide hubs con contenido fino (226-277 palabras)

**Descripción:** Los guide hubs no-ES tienen menos de 300 palabras. El contenido de guías existe (views/guide_simple.php, guide_calculator.php) pero los hubs listing no enlazan a contenido con suficiente intro.

**Solución propuesta:** Ampliar `hub_intro` y `hub_p` en `app/content/hubs/{lang}.php` para cada idioma.

---

## 10. Actualización UX de navegación (2026-05-29)

- Selector contextual en guías corregido mediante equivalencia de rutas de guía por idioma.
- Eliminadas tarjetas futuras de hubs de guías: se muestran solo 3 guías reales por idioma.
- Menú principal unificado con la estructura ES en todos los idiomas, conservando solo labels y URLs localizadas.

### P3 — Non-ES locality pages sin nearby links

**Descripción:** Las páginas de localidad EN/DE/NL/RU/NO no tienen sección de nearby localities.

**Solución propuesta:** Crear función `patch_nonES_locality_nearby($html, $path, $lang)` con mapa de nearby localidades (equivalente al mapa ES de `patch_es_p1_location_page`) con URLs localizadas.

---

## 10. CHECKLIST DE ENTREGA

- [x] Git estado limpio en entrada (HEAD = bfbc545)
- [x] Bug 1 corregido: zone_visual ES links en hubs no-ES
- [x] Bug 2 corregido: p1-internal-links guard
- [x] PHP lint — 0 errores en todos los .php
- [x] Validación en servidor local (localhost:8787)
- [x] Sin commits, sin deploy, sin cambios en producción
- [x] Sin cambios en sitemap, robots.txt, Turnstile, CSRF
- [x] Sin nuevas páginas creadas
- [x] Sin snapshots eliminados
- [x] Sin .env ni secrets tocados
- [x] Documentación creada (este archivo)

---

*Audit generado automáticamente por análisis de Sesión 7 del agente Copilot.*
