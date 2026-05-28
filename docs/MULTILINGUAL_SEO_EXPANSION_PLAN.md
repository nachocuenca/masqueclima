# Plan de expansión SEO multiidioma — Masqueclima

**Rama:** `fix/legacy-php-dev-stabilization`  
**Entorno:** dev local (`localhost:8787`) — NO producción, NO Nicalia  
**Idiomas activos:** `es`, `en`, `de`, `nl`, `ru`, `no`

---

## 1. Qué se ha construido

### 1.1 Hubs de idioma (nuevos)

Ficheros nuevos en `app/content/hubs/`:

| Archivo | Hubs incluidos |
|---|---|
| `en.php` | `/en/services/`, `/en/areas/`, `/en/guides/` |
| `de.php` | `/de/dienstleistungen/`, `/de/gebiete/`, `/de/ratgeber/` |
| `nl.php` | `/nl/diensten/`, `/nl/gebieden/`, `/nl/gidsen/` |
| `ru.php` | `/ru/uslugi/`, `/ru/raiony/`, `/ru/gidy/` |
| `no.php` | `/no/tjenester/`, `/no/omrader/`, `/no/guider/` |

Cada fichero define un array con 3 entradas de hub, con `title`, `description`, `h1`, `hub_intro`, `hub_kicker`, `hub_h2`, `hub_p`, `hub_type`, `lang`, `breadcrumb`, `hero_image`, `hero_alt`, `visual_slot`, `image_position`.

Todos los hubs validan HTTP 200, canonical correcto, kicker y CTA localizados.

### 1.2 Páginas de servicio multiidioma (extendidas + nueva)

| Archivo | Servicios definidos (5) |
|---|---|
| `app/content/services/en.php` | installation, maintenance, repair, aerothermal, solar-thermal |
| `app/content/services/de.php` | installation, wartung, reparatur, aerothermie, solar-thermie |
| `app/content/services/nl.php` | installatie, onderhoud, reparatie, aerothermie, zonne-thermisch |
| `app/content/services/ru.php` | ustanovka, tekhobsluzhivanie, remont, aerotermiia, solnechnaia-termiia |
| `app/content/services/no.php` | installasjon, vedlikehold, reparasjon, aerotermisk, soltermisk **(nuevo)** |

Formato de clave: path completo (p. ej. `/en/services/air-conditioning-installation/`).  
Cada servicio incluye: `title`, `description`, `h1`, `subtitle`, `image`, `image_alt`, `process`, `faq` (3 ítems), `canonical`, `lang`, `slug`.

Todos los 25 service pages (5 idiomas × 5 servicios) validan HTTP 200, sin castellano colado, JSON-LD `Service:OK + FAQ:OK`.

### 1.3 Infraestructura PHP añadida

#### `app/helpers.php`
- **`localized_hub_url(string $lang, string $hub): ?string`** — devuelve la URL del hub correspondiente al idioma. Cubre los 3 hubs × 6 idiomas. Retorna `null` si el par no existe.

#### `app/front_controller.php` — funciones añadidas/modificadas

| Función | Cambio |
|---|---|
| `lang_hub_pages()` | Carga y cachea `app/content/hubs/{lang}.php` |
| `lang_service_pages(string $lang)` | Carga y cachea `app/content/services/{lang}.php` |
| `render_lang_hub_page(string $path, string $lang)` | Despacha al renderer correcto según `hub_type` (services/zones/guides) |
| `lang_hub_services_body(array $page, string $lang)` | Renderiza hub de servicios con cards localizadas |
| `lang_hub_zones_body(array $page, string $lang)` | Renderiza hub de zonas con locality pills localizadas |
| `lang_hub_guides_body(array $page, string $lang)` | Renderiza hub de guías (sin guide cards en castellano) |
| `lang_service_cards_html(string $lang)` | Genera el grid de tarjetas de servicio para el hub |
| `lang_locality_pills_html(string $lang)` | Genera los pills/links de localidad usando el prefijo correcto por idioma |
| `render_service_detail_body(array $page, string $lang)` | **Modificado** — añadido parámetro `$lang`; computa `$priorityZones`, `$otherServices`, `$servicesHubUrl`, `$zonesHubUrl`, `$serviceUi` localizados |

#### `app/front_controller.php` — routing añadido

El front-controller detecta rutas de hub no-ES y de servicio no-ES y las despacha a las funciones correctas. Las rutas 404 no se tocan.

### 1.4 Vistas corregidas

#### `views/service_detail.php`
- **14 strings hardcoded en castellano** reemplazados por variables dinámicas del array `$serviceUi`.
- Añadidos guards `isset()` con fallbacks ES para compatibilidad standalone.
- Kicker, CTAs, títulos de sección, URLs de hub: todo localizado.

#### `views/partials/hub_hero.php`
- `"Pide presupuesto sin compromiso"` → `t('cta.quote', '...')`
- `"Escríbenos por WhatsApp"` (texto + aria-label) → `t('cta.whatsapp', '...')`
- Claves ya existían en todos los ficheros de traducción.

### 1.5 Traducciones añadidas

| Archivo | Claves añadidas |
|---|---|
| `app/translations/en.php` | `nav.services`, `nav.zones`, `nav.guides` (+ `cta.*` ya existían) |
| `app/translations/de.php` | `nav.services` (idem) |
| `app/translations/nl.php` | `nav.services` (idem) |
| `app/translations/ru.php` | `nav.services` (idem) |
| `app/translations/no.php` | `nav.services`, `nav.zones`, `nav.guides` (idem) |

---

## 2. Arquitectura de URLs

### Hubs

| Hub key | ES | EN | DE | NL | RU | NO |
|---|---|---|---|---|---|---|
| `services` | `/es/servicios/` | `/en/services/` | `/de/dienstleistungen/` | `/nl/diensten/` | `/ru/uslugi/` | `/no/tjenester/` |
| `zones` | `/es/zonas/` | `/en/areas/` | `/de/gebiete/` | `/nl/gebieden/` | `/ru/raiony/` | `/no/omrader/` |
| `guides` | `/es/blog/` | `/en/guides/` | `/de/ratgeber/` | `/nl/gidsen/` | `/ru/gidy/` | `/no/guider/` |

### Servicios (ejemplo: instalación)

| Lang | URL |
|---|---|
| ES | `/es/servicios/instalacion-aire-acondicionado/` |
| EN | `/en/services/air-conditioning-installation/` |
| DE | `/de/dienstleistungen/klimaanlage-installation/` |
| NL | `/nl/diensten/airco-installatie/` |
| RU | `/ru/uslugi/ustanovka-konditsionera/` |
| NO | `/no/tjenester/aircondition-installasjon/` |

### Locality landings (prefijo por idioma)

| Lang | Prefijo |
|---|---|
| ES | `/es/aire-acondicionado-` |
| EN | `/en/air-conditioning-` |
| DE | `/de/klimaanlage-` |
| NL | `/nl/airco-` |
| RU | `/ru/konditsioner-` |
| NO | `/no/aircondition-` |

21 snapshots por idioma no-ES (mismo conjunto de municipios que ES). Sirven HTTP 200 desde `app/snapshots/`.

---

## 3. Validación realizada

### Criterios de PASS por página

| Criterio | Método |
|---|---|
| HTTP 200 | `Invoke-WebRequest` |
| Sin castellano colado (visible) | Grep en response HTML |
| Canonical correcto | Grep `<link rel="canonical"` |
| JSON-LD Service + FAQ | Grep `@type.*Service` + `@type.*FAQPage` |
| CTA localizado (hub pages) | Grep `Pide presupuesto` ausente |

### Resultados

| Grupo | Total | PASS | FAIL |
|---|---|---|---|
| Hub pages (5 idiomas × 3 hubs) | 15 | 15 | 0 |
| Service pages (5 idiomas × 5 servicios) | 25 | 25 | 0 |
| Locality snapshots (spot test) | ~10 | 10 | 0 |
| PHP lint (todos los .php) | todos | 0 errores | — |
| BOM check | todos | 0 BOMs | — |

---

## 4. Qué NO se ha hecho (fuera de scope)

| Item | Motivo / Estado |
|---|---|
| Hreflang entre páginas equivalentes multiidioma | No implementado — requiere decisión de si se consideran equivalentes o mercados independientes |
| Sitemap actualizado con URLs multiidioma | Fuera de scope — NO tocar sitemap |
| Locality patching multiidioma (title/meta únicos) | Snapshots sirven; el patch dinámico de metadata para no-ES es fase posterior |
| Guide cards en idiomas no-ES | Eliminadas (eran placeholders en castellano); sólo se muestra texto descriptivo del hub |
| OG images por idioma/hub | Requiere assets gráficos 1200×630 |
| Diseño / layout | No modificado |
| Deploy, commit, producción, Nicalia | Ninguno — todo en local dev |

---

## 5. Deuda técnica documentada

| Deuda | Prioridad | Acción recomendada |
|---|---|---|
| Hreflang para hubs y servicios multiidioma | P1 | Decidir estrategia y añadir en `render_lang_hub_page()` y `render_service_detail_body()` |
| Locality patching multiidioma (title/H1 únicos) | P1 | Extender `patch_snapshot_html()` para idiomas no-ES |
| Guide cards en idiomas (contenido real cuando existan guías) | P2 | Añadir claves `guide_cards` en `content/hubs/{lang}.php` |
| Sitemap con URLs multiidioma | P2 | Regenerar antes de go-live con todas las URLs |
| OG images por idioma | P3 | Assets gráficos necesarios |

---

## 6. Cómo arrancar el entorno de desarrollo

```powershell
cd C:\Users\ignac\Desktop\masqueclima_repo
php -S localhost:8787 -t public public/index.php
```

Verificación rápida:

```powershell
# Hub servicios EN
(Invoke-WebRequest "http://localhost:8787/en/services/" -UseBasicParsing).StatusCode

# Servicio DE
(Invoke-WebRequest "http://localhost:8787/de/dienstleistungen/klimaanlage-installation/" -UseBasicParsing).StatusCode
```

---

*Documento generado durante sesión de validación de expansión multiidioma. Sin deploy, sin commit, sin producción.*
