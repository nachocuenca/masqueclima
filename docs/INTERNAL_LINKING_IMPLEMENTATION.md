# Internal Linking Implementation

**Branch:** `fix/legacy-php-dev-stabilization`  
**Session:** 8 — Deep internal linking (multilingual, scalable, localizado)  
**Status:** ✅ Implemented & validated locally. Not yet committed.

---

## Objetivo

Diseñar e implementar un enlazado interno robusto, localizado y escalable para:
- Todas las páginas de localidad (ES y no-ES, 20 localidades × 6 idiomas)
- Todas las páginas de servicio (5 servicios × 6 idiomas)
- Todos los hubs (zonas, servicios, guías — 3 hubs × 6 idiomas)

---

## Arquitectura de Enlazado por Tipo de Página

### 1. Páginas de Localidad ES (`/es/aire-acondicionado-{slug}/`)

**Función:** `insert_p1_internal_links()` en `app/front_controller.php`  
**Guard:** `!str_contains($html, 'p1-internal-links')`  
**Clase CSS:** `p1-internal-links`, pills: `p1-pill`

**Links inyectados (hardcoded ES):**
- 5 páginas de servicio individuales:
  - `/es/servicios/instalacion-aire-acondicionado/`
  - `/es/servicios/mantenimiento-climatizacion/`
  - `/es/servicios/reparacion-aire-acondicionado/`
  - `/es/servicios/aerotermia-bomba-calor/`
  - `/es/servicios/energia-solar-termica/`
- Hub "Todos los servicios": `/es/servicios/`
- Hub "Todas las zonas": `/es/zonas/`
- 3 localidades cercanas (de `nearby_locality_slugs()`)

**Total pills por página:** ~10 links

---

### 2. Páginas de Localidad No-ES (`/{lang}/{term}-{slug}/`)

**Función:** `build_locality_links_block()` + `patch_nonES_locality_seo()` en `app/front_controller.php`  
**Guard:** `!str_contains($html, 'locality-links-block')`  
**Clase CSS:** `locality-links-block`, pills: `loc-pill`  
**Inserción:** Antes de `<section class="zona" id="zona">` o antes de `</main>`

**Links inyectados (localizados por idioma):**
- 5 páginas de servicio en el idioma correspondiente (de `localized_service_equivalent_paths()`)
- Hub de zonas localizado (de `localized_hub_url($lang, 'zones')`)
- 3 localidades cercanas en el idioma correspondiente (de `localized_locality_prefixes()[$lang]` + `nearby_locality_slugs()`)
- Botón CTA al hub de servicios localizado

**Idiomas:** en, de, nl, ru, no (ES usa `insert_p1_internal_links`)  
**Total pills por página:** ~9 links

---

### 3. Páginas de Detalle de Servicio (`/{lang}/servicios/{slug}/` etc.)

**Vista:** `views/service_detail.php`  
**Sección:** `service-areas` — hub-links div

**Links presentes:**
- Hub "Todos los servicios" (localizado)
- Hub "Todas las zonas" (localizado)
- **[NUEVO]** Hub de guías/blog (localizado) — `$guidesUrl` + `$guidesLabel` inyectados desde `render_service_detail_body()`
- 5 localidades prioritarias (de `localized_service_equivalent_paths()`)
- Sección relacionada: 4 servicios hermanos

**Cambio en `render_service_detail_body()`:**
```php
$guidesUrl  = localized_hub_url($lang, 'guides');
$guidesLabel = match ($lang) { 'en' => 'Guides', 'de' => 'Ratgeber', ... };
```

---

### 4. Hub ES Zonas (`/es/zonas/`)

**Función:** `hub_zones_body()` en `app/front_controller.php`

**[NUEVO] Sección añadida al heredoc:**
```html
<section class="hub-section" aria-labelledby="zonas-servicios">
  <div class="container">
    <p class="hub-kicker">Servicios</p>
    <h2 class="section-title" id="zonas-servicios">Servicios de climatización disponibles</h2>
    <div class="hub-links">
      <!-- 5 service pills (ES) -->
      <a class="hub-pill" href="/es/servicios/">Todos los servicios</a>
    </div>
  </div>
</section>
```

**Variables:** `$esServicePills` generado desde `localized_service_equivalent_paths()['es']` + `locality_service_link_labels()['es']`

---

### 5. Hubs No-ES de Zonas (`/{lang}/areas/` etc.)

**Función:** `lang_hub_areas_body()` en `app/front_controller.php`

**[NUEVO] Sección añadida (ID:** `lang-areas-services`)  
**Variables:** `$svcUrls2`, `$svcLabels2`, `$svcHubUrl2`, `$langServicePills`  
**Kicker/H2/label:** localizados por idioma via `match($lang)`

---

### 6. Hubs No-ES de Guías (`/{lang}/guides/` etc.)

**Función:** `lang_hub_guides_body()` en `app/front_controller.php`

**[NUEVO] Sección añadida (ID:** `lang-guides-services`, clase `alt`)  
**Variables:** `$guidesSvcPills`, `$guidesRelatedKicker`, `$guidesRelatedH2`, `$guidesRelatedP`, `$guidesAllServicesLabel`

---

## Nuevas Funciones Añadidas

### `nearby_locality_slugs(string $slug): array`
**Archivo:** `app/helpers.php`  
**Propósito:** Mapa de vecindad entre las 20 localidades. Devuelve lista de slugs próximos.  
**Guard:** `function_exists('nearby_locality_slugs')`  
**Default fallback:** `['benidorm', 'altea', 'calpe', 'finestrat', 'la-nucia']`

### `locality_service_link_labels(): array`
**Archivo:** `app/front_controller.php`  
**Propósito:** Labels localizados para 5 tipos de servicio × 6 idiomas.  
**Estructura:** `[lang][serviceKey] => label`

### `build_locality_links_block(string $lang, string $slug, string $city): string`
**Archivo:** `app/front_controller.php`  
**Propósito:** Genera el bloque HTML completo de enlazado interno para páginas de localidad no-ES.  
**Depende de:** `localized_service_equivalent_paths()`, `locality_service_link_labels()`, `nearby_locality_slugs()`, `localized_locality_prefixes()`, `localized_hub_url()`, `locality_display_names()`

---

## Condiciones de Guard (idempotencia)

| Página | Guard condition |
|--------|----------------|
| ES locality | `!str_contains($html, 'p1-internal-links')` |
| Non-ES locality | `!str_contains($html, 'locality-links-block')` |
| Service detail | Variables inyectadas en cada render (sin guard necesario) |
| Hub ES zonas | Heredoc estático (no guard) |
| Hub non-ES areas | Heredoc estático (no guard) |
| Hub non-ES guides | Heredoc estático (no guard) |

---

## Resultados de Validación

| URL | Check | Resultado |
|-----|-------|-----------|
| `/es/aire-acondicionado-benidorm/` | p1-pill count | 12 ✅ |
| `/es/aire-acondicionado-benidorm/` | instalacion-aire-acondicionado | 1 ✅ |
| `/en/air-conditioning-benidorm/` | locality-links-block | 5 (4 CSS + 1 class) ✅ |
| `/en/air-conditioning-benidorm/` | air-conditioning-installation | 1 ✅ |
| `/en/air-conditioning-benidorm/` | ES service URLs en body | 0 ✅ |
| `/de/klimaanlage-benidorm/` | locality-links-block | 5 ✅ |
| `/de/klimaanlage-benidorm/` | klimaanlage-installation | 1 ✅ |
| `/de/klimaanlage-benidorm/` | ES service URLs | 0 ✅ |
| `/nl/airco-benidorm/` | locality-links-block | 5 ✅ |
| `/nl/airco-benidorm/` | airco-installatie | 1 ✅ |
| `/es/servicios/instalacion-aire-acondicionado/` | /es/blog/ link | 2 ✅ |
| `/en/services/air-conditioning-installation/` | /en/guides/ link | 3 ✅ |
| `/es/zonas/` | zonas-servicios section | 2 ✅ |
| `/es/zonas/` | instalacion-aire-acondicionado | 1 ✅ |
| `/en/areas/` | lang-areas-services section | 2 ✅ |
| `/en/areas/` | air-conditioning-installation | 1 ✅ |
| `/en/guides/` | lang-guides-services section | 2 ✅ |
| `/no/guider/` | lang-guides-services section | 2 ✅ |
| PHP lint | front_controller.php, helpers.php, service_detail.php | ✅ Clean |

---

## Archivos Modificados

| Archivo | Cambios |
|---------|---------|
| `app/helpers.php` | +`nearby_locality_slugs()` |
| `app/front_controller.php` | +`locality_service_link_labels()`, +`build_locality_links_block()`, mod `insert_p1_internal_links()`, mod `patch_nonES_locality_seo()`, mod `hub_zones_body()`, mod `lang_hub_areas_body()`, mod `lang_hub_guides_body()`, mod `render_service_detail_body()` |
| `views/service_detail.php` | +guides hub pill en service-areas section |

---

## Notas SEO

- Todos los links son `<a href="...">` — no `nofollow`, no JavaScript
- Las URLs no-ES usan siempre el equivalente localizado, nunca `/es/`
- El número de links por sección es moderado (5–10 por bloque)
- Sin links rotos: todas las URLs apuntan a rutas existentes validadas
