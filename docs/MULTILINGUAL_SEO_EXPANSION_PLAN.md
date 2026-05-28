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
| NO | `/no/tjenester/installasjon-av-aircondition/` |

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

## 7. Correccion visual/UX post-expansion - 2026-05-28

Alcance aplicado en `fix/legacy-php-dev-stabilization`, sin commit y sin deploy:

- Selector de idioma contextual: `localized_equivalent_url()` conserva equivalentes seguros para home, hubs, servicios y landings locales de aire acondicionado. Si una localidad no existe en el idioma destino, cae al hub de zonas de ese idioma.
- Footer multiidioma: queda reducido a 3 enlaces por idioma: Servicios/Zonas/Guias y equivalentes EN/DE/NL/RU/NO. Se eliminan Home/FAQ/Contacto/Kontakt del footer generado.
- Homes EN/DE/NL/RU/NO: se anaden 3 accesos contextuales en el cuerpo a servicios, zonas/areas y guias, reutilizando los puntos donde ES ya enlaza esos hubs.
- Cards de servicios no-ES: el hub de servicios usa la misma estructura visual que ES (`hub-grid services-grid` + `hub-card`) y deja de usar `.service-card`, evitando los bloques grises del estilo clasico.
- CTA final: `final_budget_cta_html($lang)` ahora renderiza copy localizado para ES/EN/DE/NL/RU/NO y se inserta antes del footer en hubs y paginas de servicio dinamicas, sin duplicar snapshots existentes.
- Modal de presupuesto: se normalizan textos visibles por idioma en el HTML servido, preservando `csrf`, honeypot, `POST`, `return_to` y endpoint.

Validado localmente en `localhost:8787`:

- 5 homes no-ES: `/en/`, `/de/`, `/nl/`, `/ru/`, `/no/`.
- 15 hubs no-ES.
- 5 servicios de instalacion solicitados.
- Selector contextual: 20/20 equivalencias esperadas.
- Smoke ES/NO: 9/9 rutas con 200, footer de 3 enlaces y sin warnings PHP.

Pendientes:

- No se tocaron sitemap, robots, DNS, `.env`, produccion ni Nicalia.
- No se anadieron articulos de guia ni equivalencias de articulos inexistentes.

---

*Documento generado durante sesión de validación de expansión multiidioma. Sin deploy, sin commit, sin producción.*

## 8. Auditoria final predeploy - 2026-05-28

Referencia completa: `docs/FINAL_PREDEPLOY_AUDIT.md`.

Resultado multiidioma final:

- 45 paginas no-ES auditadas sin frases visibles en castellano del set obligatorio.
- 48 hubs/servicios con hreflang completo tras correccion.
- Selector contextual validado en los casos obligatorios de hubs, servicios, guias, zonas y Benidorm.
- 120 landings locales multiidioma auditadas con 0 errores HTTP.
- Banner de cookies heredado localizado en EN/DE/NL/RU/NO.

Pendientes:

- P1 externo al contenido multiidioma: `/politica-de-cookies` enlazado y en 404.
- P2 editorial: `app/content/guides/*` no-ES conserva borradores en castellano no renderizados actualmente.
