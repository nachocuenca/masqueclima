# OG_IMAGE_REFINEMENT.md

Implementación de `og:image` específica por tipo de página.  
**Fecha:** 2026-05-28 | **Rama:** fix/legacy-php-dev-stabilization | **Sesión:** 6

---

## Diagnóstico inicial (P2)

Todas las páginas usaban `og:image = https://masqueclima.es/assets/img/og.jpg` (imagen genérica).  
Esto ocurría porque:
- `patch_snapshot_seo_meta()` no parchaba `og:image`.
- `patch_lang_hub_head()` no parchaba `og:image`.
- `patch_es_hub_head()` no parchaba `og:image`.
- `views/layout.php` (homes) tiene `og.jpg` hardcoded — correcto para homes.

## Solución implementada

Tres cambios en `app/front_controller.php`:

### 1. Helper `patch_og_image_meta()`

```php
function patch_og_image_meta(string $html, string $absoluteUrl): string {
  $html = preg_replace('/<meta property="og:image" content="[^"]*">/i',
    '<meta property="og:image" content="' . $absoluteUrl . '">', $html, 1) ?? $html;
  $html = preg_replace('/<meta name="twitter:image" content="[^"]*">/i',
    '<meta name="twitter:image" content="' . $absoluteUrl . '">', $html, 1) ?? $html;
  return $html;
}
```

### 2. `patch_snapshot_og_image()` — localidades (todos los idiomas)

Matchea URLs de localidades en los 6 idiomas, recupera el hero image del `locality_hero_map()`, y llama a `patch_og_image_meta()`. Insertado en el pipeline principal tras `patch_locality_hero_image()`.

### 3. Integración en `patch_lang_hub_head()` — hubs/servicios non-ES

```php
$html = patch_es_hub_hero_preload($html, $page);
if (!empty($page['hero_image'])) {
  $html = patch_og_image_meta($html, 'https://masqueclima.es' . $page['hero_image']);
}
```

### 4. Integración en `patch_es_hub_head()` — hubs/servicios ES

Mismo patrón que el punto 3, añadido tras `patch_es_hub_hero_preload()`.

---

## Inventario de imágenes utilizadas

| Tipo de página | og:image |
|----------------|----------|
| Home (6 langs) | `og.jpg` (generic — correcto) |
| Hub servicios (6 langs) | `heroes/hub-servicios-climatizacion.webp` |
| Hub zonas (6 langs) | `heroes/hub-zonas-marina-baixa.webp` |
| Hub guías (6 langs) | `heroes/hub-guias-climatizacion.webp` |
| Servicio instalación | `services/service-instalacion-aire-acondicionado.webp` |
| Servicio mantenimiento | `services/service-mantenimiento-climatizacion.webp` |
| Servicio reparación | `services/service-reparacion-aire-acondicionado.webp` |
| Servicio aerotermia | `services/service-aerotermia-bomba-calor.webp` |
| Servicio solar | `services/service-energia-solar-termica.webp` |
| Localidades (120 URLs) | `localidades/heroes/hero-localidad-{slug}.webp` |
| Legales (18 URLs) | `og.jpg` (generic — aceptable) |

Todos los assets validados como existentes en `public/assets/img/`.

---

## Validación localhost:8787

| URL | og:image | twitter:image = og:image |
|-----|----------|--------------------------|
| `/es/aire-acondicionado-benidorm/` | `hero-localidad-benidorm.webp` | ✅ |
| `/en/air-conditioning-benidorm/` | `hero-localidad-benidorm.webp` | ✅ |
| `/de/klimaanlage-benidorm/` | `hero-localidad-benidorm.webp` | ✅ |
| `/nl/airco-benidorm/` | `hero-localidad-benidorm.webp` | ✅ |
| `/ru/konditsioner-benidorm/` | `hero-localidad-benidorm.webp` | ✅ |
| `/no/aircondition-benidorm/` | `hero-localidad-benidorm.webp` | ✅ |
| `/es/servicios/` | `hub-servicios-climatizacion.webp` | ✅ |
| `/en/services/` | `hub-servicios-climatizacion.webp` | ✅ |
| `/de/dienstleistungen/` | `hub-servicios-climatizacion.webp` | ✅ |
| `/es/zonas/` | `hub-zonas-marina-baixa.webp` | ✅ |
| `/en/areas/` | `hub-zonas-marina-baixa.webp` | ✅ |
| `/es/blog/` | `hub-guias-climatizacion.webp` | ✅ |
| `/en/guides/` | `hub-guias-climatizacion.webp` | ✅ |
| `/es/servicios/instalacion-aire-acondicionado/` | `service-instalacion-aire-acondicionado.webp` | ✅ |
| `/en/services/air-conditioning-installation/` | `service-instalacion-aire-acondicionado.webp` | ✅ |
| `/es/servicios/mantenimiento-climatizacion/` | `service-mantenimiento-climatizacion.webp` | ✅ |
| `/es/` | `og.jpg` | ✅ |
| `/en/` | `og.jpg` | ✅ |

PHP lint post-implementación: `No syntax errors detected` ✅  
Sitemap: sin cambios ✅  
Robots: sin cambios ✅

---

## Pendientes

- Verificar en dev VPS tras next deploy (og:image no estaba siendo validada en VPS antes de esta sesión).
- Legales con `og.jpg` — aceptable. Se puede mejorar a futuro con imagen corporativa.
