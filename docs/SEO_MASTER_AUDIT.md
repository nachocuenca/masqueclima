# SEO Master Audit — Masqueclima Dev
**Fecha:** 2026-05-28  
**Rama:** `fix/legacy-php-dev-stabilization`  
**Entorno auditado:** `dev.masqueclima.es` (NO producción/Nicalia)  
**Auditor:** GitHub Copilot — perfil: SEO senior técnico + local + CRO

---

## 1. Resumen ejecutivo

La web tiene una arquitectura sólida para SEO local: front-controller PHP, snapshots HTML, patching dinámico de meta tags, hreflang correcto en snapshots de área, heroes locales por municipio, y secciones de CTA funcionales. No hay errores PHP. La conversión (popup, WhatsApp, formulario) está intacta.

**El problema central antes de este audit:** 15 de 20 landings locales en español tenían el mismo `<title>` genérico ("Instalación y Mantenimiento de Climatización | +QUECLIMA"), lo que causaba títulos duplicados en masa y bloqueaba el posicionamiento local. Ya corregido en esta sesión.

**Estado tras las correcciones:** La web está técnicamente preparada para Nicalia, con las mejoras de P0/P1 implementadas. Las recomendaciones P2 quedan documentadas para la siguiente fase.

---

## 2. Estado técnico actual

### PHP
- Sin errores de sintaxis en ningún archivo PHP (verificado con `php -l`).
- PHP lint: **100% limpio**.

### Arquitectura de rendering
| Tipo de página | Modo de rendering |
|---|---|
| Homes (es/en/de/nl/ru/no) | Snapshot HTML con patching dinámico |
| Landings locales (20 municipios × 6 idiomas) | Snapshot HTML con patching dinámico |
| Hubs ES (Servicios, Zonas, Blog/Guías) | Renderizado dinámico desde `front_controller.php` |
| Páginas de servicio ES (5 servicios) | Renderizado dinámico desde `views/service_detail.php` |
| Reformas | Snapshot existente, sin enlace en nav |

### Noindex en dev
- El front-controller inyecta `<meta name="robots" content="noindex, nofollow, noarchive">` cuando `APP_ENV !== 'production'`. **Correcto.**

---

## 3. Inventario de URLs

### Homes
| URL | Existe | Hreflang | Canónica |
|---|---|---|---|
| `/es/` | ✓ snapshot | ✓ completo | ✓ |
| `/en/` | ✓ snapshot | ✓ completo | ✓ |
| `/de/` | ✓ snapshot | ✓ completo | ✓ |
| `/nl/` | ✓ snapshot | ✓ completo | ✓ |
| `/ru/` | ✓ snapshot | ✓ completo | ✓ |
| `/no/` | ✓ snapshot | ✓ completo | ✓ |

### Hubs ES (renderizado dinámico)
| URL | Existe | H1 | Canónica | JSON-LD |
|---|---|---|---|---|
| `/es/servicios/` | ✓ dinámico | ✓ único | ✓ | BreadcrumbList + (heredado) |
| `/es/zonas/` | ✓ dinámico | ✓ único | ✓ | BreadcrumbList + (heredado) |
| `/es/blog/` | ✓ dinámico | ✓ único | ✓ | BreadcrumbList + (heredado) |

Nota: Los hubs eliminan el bloque hreflang del snapshot base (son páginas solo en ES). **Correcto.**

### Servicios ES (renderizado dinámico)
| URL | Title único | H1 único | FAQ | FAQPage JSON-LD |
|---|---|---|---|---|
| `/es/servicios/instalacion-aire-acondicionado/` | ✓ | ✓ | ✓ 3 FAQs | ✓ **añadido** |
| `/es/servicios/mantenimiento-climatizacion/` | ✓ | ✓ | ✓ 3 FAQs | ✓ **añadido** |
| `/es/servicios/reparacion-aire-acondicionado/` | ✓ | ✓ | ✓ 3 FAQs | ✓ **añadido** |
| `/es/servicios/aerotermia-bomba-calor/` | ✓ | ✓ | ✓ 3 FAQs | ✓ **añadido** |
| `/es/servicios/energia-solar-termica/` | ✓ | ✓ | ✓ 3 FAQs | ✓ **añadido** |

### Landings locales ES — 20 municipios
| URL | Title único | H1 único | Canónica | Hero local | Nearby links |
|---|---|---|---|---|---|
| `/es/aire-acondicionado-benidorm/` | ✓ | ✓ | ✓ | ✓ | ✓ |
| `/es/aire-acondicionado-altea/` | ✓ | ✓ | ✓ | ✓ | ✓ |
| `/es/aire-acondicionado-calpe/` | ✓ | ✓ | ✓ | ✓ | ✓ |
| `/es/aire-acondicionado-finestrat/` | ✓ | ✓ | ✓ | ✓ | ✓ |
| `/es/aire-acondicionado-la-nucia/` | ✓ | ✓ | ✓ | ✓ | ✓ |
| `/es/aire-acondicionado-albir/` | ✓ **corregido** | ✓ | ✓ | ✓ | ✓ **añadido** |
| `/es/aire-acondicionado-alfaz-del-pi/` | ✓ **corregido** | ✓ | ✓ | ✓ | ✓ **añadido** |
| `/es/aire-acondicionado-beniarda/` | ✓ **corregido** | ✓ | ✓ | ✓ | ✓ **añadido** |
| `/es/aire-acondicionado-benifato/` | ✓ **corregido** | ✓ | ✓ | ✓ | ✓ **añadido** |
| `/es/aire-acondicionado-benimantell/` | ✓ **corregido** | ✓ | ✓ | ✓ | ✓ **añadido** |
| `/es/aire-acondicionado-bolulla/` | ✓ **corregido** | ✓ | ✓ | ✓ | ✓ **añadido** |
| `/es/aire-acondicionado-callosa-den-sarria/` | ✓ **corregido** | ✓ | ✓ | ✓ | ✓ **añadido** |
| `/es/aire-acondicionado-confrides/` | ✓ **corregido** | ✓ | ✓ | ✓ | ✓ **añadido** |
| `/es/aire-acondicionado-guadalest/` | ✓ **corregido** | ✓ | ✓ | ✓ | ✓ **añadido** |
| `/es/aire-acondicionado-orxeta/` | ✓ **corregido** | ✓ | ✓ | ✓ | ✓ **añadido** |
| `/es/aire-acondicionado-polop/` | ✓ **corregido** | ✓ | ✓ | ✓ | ✓ **añadido** |
| `/es/aire-acondicionado-relleu/` | ✓ **corregido** | ✓ | ✓ | ✓ | ✓ **añadido** |
| `/es/aire-acondicionado-sella/` | ✓ **corregido** | ✓ | ✓ | ✓ | ✓ **añadido** |
| `/es/aire-acondicionado-tarbena/` | ✓ **corregido** | ✓ | ✓ | ✓ | ✓ **añadido** |
| `/es/aire-acondicionado-villajoyosa/` | ✓ **corregido** | ✓ | ✓ | ✓ | ✓ **añadido** |

### Landings locales multilingüe (EN/DE/NL/RU/NO)
120 snapshots en 5 idiomas adicionales. Títulos, H1 y canónicas en los snapshots. Sin patching de título para idiomas no-ES (fuera del scope actual).

### Reformas
| URL | Estado | Acción |
|---|---|---|
| `/es/reformas-integrales-benidorm/` | Snapshot existe, accessible | Sin enlazar desde nav. No en sitemap. **No tocar.** |
| Equivalentes EN/DE/NL/RU/NO reformas | Snapshots existen | Ídem. |

---

## 4. Problemas detectados — P0 / P1 / P2

### P0 — Crítico (bloqueante para SEO)

| # | Problema | Estado |
|---|---|---|
| P0-1 | 15 landings locales ES con `<title>` idéntico genérico: "Instalación y Mantenimiento de Climatización | +QUECLIMA" | ✅ **Corregido** — 15 titles únicos añadidos |
| P0-2 | `snapshot_jsonld()` generaba BreadcrumbList con un solo ítem llamado "Home" apuntando a la URL de la landing local (incorrecto semánticamente) | ✅ **Corregido** — BreadcrumbList ahora tiene 2 ítems: Inicio → Localidad |
| P0-3 | Sin FAQPage JSON-LD en las 5 páginas de servicio (que sí tienen secciones FAQ visibles) | ✅ **Corregido** — FAQPage schema añadido dinámicamente |

### P1 — Importante (impacta posicionamiento)

| # | Problema | Estado |
|---|---|---|
| P1-1 | 15 landings locales ES sin nearby links (solo 5 ciudades tenían sección "Servicios y zonas relacionadas") | ✅ **Corregido** — todos los 20 municipios tienen nearby links |
| P1-2 | Páginas de servicio sin enlaces cruzados entre sí (solo enlazaban a /es/servicios/ y /es/zonas/) | ✅ **Corregido** — sección "Otros servicios" añadida en service_detail.php |
| P1-3 | Guide cards sin señal de "próximamente" — riesgo de contenido delgado percibido | ✅ **Corregido** — badge "Próximamente" añadido en guide_cards.php |
| P1-4 | `og:image` para hubs y páginas de servicio hereda la de la home (og.jpg genérico) | ⚠️ **Recomendado** — no implementado (requiere imágenes OG específicas por sección) |
| P1-5 | Meta description de la home ES muy genérica para local SEO | ⚠️ **Recomendado** — riesgo de regresión si se cambia el snapshot |

### P2 — Mejora (no urgente)

| # | Problema | Recomendación |
|---|---|---|
| P2-1 | Nav principal ES no incluye Servicios/Zonas/Guías (solo anclas de home) | Evaluar añadir cuando se confirme la IA del site |
| P2-2 | `/es/blog/` no está en sitemap.xml | Añadir cuando haya al menos 1 guía publicada |
| P2-3 | Servicios no están en sitemap.xml | Añadir en siguiente versión de sitemap |
| P2-4 | Hreflang para hubs y servicios (páginas solo-ES) | No aplica hreflang para páginas monolingüe — correcto omitirlo |
| P2-5 | No hay `LocalBusiness` JSON-LD con `address` o `geo` | Añadir solo si se tiene dirección física verificable |
| P2-6 | `og:image` específica por página de servicio | Crear imágenes 1200×630 para cada servicio |
| P2-7 | Twitter Card type es solo `summary` — debería ser `summary_large_image` en páginas ricas | Cambiar en snapshots en próxima regeneración |
| P2-8 | `<link rel="preload">` para hero hub pages (Servicios, Zonas, Guías) no existe | `patch_es_hub_hero_preload` ya lo añade si el asset existe — verificar en runtime |
| P2-9 | BreadcrumbList visual (partial `breadcrumbs.php`) no se usa en landings snapshot | Requiere integración en snapshot patching pipeline |
| P2-10 | Las guías no tienen fechas de publicación (`datePublished`) en JSON-LD | Añadir cuando se publiquen guías reales |

---

## 5. Cambios implementados en esta sesión

### `app/front_controller.php`
1. **Extendida `patch_es_p1_location_page()`** — de 5 a 20 municipios con títulos, meta descriptions y nearby links únicos.
2. **Añadido FAQPage JSON-LD en `es_page_jsonld()`** — se genera automáticamente a partir del array `$page['faq']` de cada servicio.
3. **Corregida `snapshot_jsonld()`** — BreadcrumbList ahora genera 2 ítems para landings locales (Inicio + Localidad) en vez de 1 ítem incorrecto.

### `views/service_detail.php`
4. **Añadida sección "Otros servicios"** — enlaces cruzados a los 4 servicios restantes en cada página de servicio.

### `views/partials/guide_cards.php`
5. **Añadido badge "Próximamente"** — cada guide card muestra el estado para evitar percepción de contenido delgado.

---

## 6. Cambios recomendados no implementados

| Cambio | Motivo de no implementación | Prioridad |
|---|---|---|
| Mejorar title/meta home ES para local SEO | Requiere editar snapshot — riesgo de regresión visual y conversión | P1 |
| Añadir `og:image` por servicio/hub | No hay imágenes 1200×630 preparadas | P1 |
| Añadir Servicios/Zonas/Guías al nav ES | Cambio de layout — no en scope | P2 |
| Sitemap nuevo con servicios y zonas | Requiere aprobación + validación completa | P2 |
| Noindex en reformas | Requiere decisión de negocio | P2 |
| LocalBusiness con geo/address | No hay datos verificados | P2 |

---

## 7. Plan de sitemap futuro

Versión sugerida para cuando llegue a producción:

```
/es/                         priority=1.0
/en/                         priority=0.9
/de/                         priority=0.9
/nl/                         priority=0.9
/ru/                         priority=0.8
/no/                         priority=0.8
/es/servicios/               priority=0.85
/es/zonas/                   priority=0.85
/es/servicios/instalacion-aire-acondicionado/   priority=0.8
/es/servicios/mantenimiento-climatizacion/      priority=0.8
/es/servicios/reparacion-aire-acondicionado/    priority=0.8
/es/servicios/aerotermia-bomba-calor/           priority=0.75
/es/servicios/energia-solar-termica/            priority=0.75
/es/aire-acondicionado-benidorm/    priority=0.85  (+ hreflang multilingüe)
/es/aire-acondicionado-altea/       priority=0.8
/es/aire-acondicionado-calpe/       priority=0.8
/es/aire-acondicionado-finestrat/   priority=0.75
/es/aire-acondicionado-la-nucia/    priority=0.75
... (resto de municipios a priority=0.7)
/es/blog/ + guías individuales      (cuando existan artículos reales)
```

NO incluir en sitemap:
- `/es/reformas-integrales-benidorm/` ni sus equivalentes multilingüe
- `/es/blog/` hasta que haya al menos 1 guía publicada con URL propia

---

## 8. Plan de guías — prioridad por impacto SEO/comercial

| # | Título sugerido | Intención | Prioridad |
|---|---|---|---|
| 1 | ¿Cuánto cuesta instalar un aire acondicionado en 2025? | Transaccional alta | P0 |
| 2 | Mantenimiento del aire acondicionado antes del verano: qué revisar | Informacional + estacional | P0 |
| 3 | Qué potencia de aire acondicionado necesita mi vivienda | Informacional + cualificante | P1 |
| 4 | Split, multisplit o conductos: cuál elegir para tu caso | Informacional + cualificante | P1 |
| 5 | Aire acondicionado para apartamentos turísticos en la Costa Blanca | Local + comercial | P1 |
| 6 | Aerotermia y bomba de calor: cuándo merece la pena en la Costa Blanca | Informacional + comercial | P1 |
| 7 | Mi aire acondicionado no enfría: causas y soluciones | Problema frecuente | P2 |
| 8 | Consumo de un aire acondicionado inverter: cómo calcularlo | Informacional | P2 |
| 9 | Bomba de calor para calefacción en invierno: ¿funciona en Alicante? | Informacional local | P2 |
| 10 | Solar térmica para agua caliente sanitaria: guía básica | Informacional | P3 |

**Nota:** Cada guía debe tener URL propia (`/es/blog/[slug]/`), mínimo 800 palabras útiles, H1 natural, FAQ al final y CTA visible. No publicar la URL hasta que el contenido esté completo.

---

## 9. Plan de mejora de landings locales — siguiente fase

### Estado actual (tras este audit)
- Todas las 20 landings locales ES tienen: title único ✓ / meta única ✓ / H1 ✓ / canonical ✓ / hero local ✓ / nearby links ✓ / JSON-LD BreadcrumbList ✓

### Mejoras pendientes para landings locales (no implementadas)
1. **Contenido de texto diferenciado por municipio** — actualmente el cuerpo del snapshot es similar en estructura para todas las localidades. A futuro, municipios priority (Benidorm, Altea, Calpe, Finestrat, La Nucía) deberían tener 1-2 párrafos adicionales con señales locales reales (barrios, tipo de vivienda, uso turístico, etc.).
2. **Enlace desde landings locales a servicios específicos** — "¿Buscas instalar aire acondicionado en Benidorm? → [Instalación]".
3. **FAQ por localidad** — al menos 2-3 preguntas con contexto local.
4. **Integrar BreadcrumbList visual** (`views/partials/breadcrumbs.php`) en el pipeline de snapshots.

---

## 10. Riesgos antes de Nicalia (producción)

| Riesgo | Severidad | Mitigación |
|---|---|---|
| `APP_ENV` no configurado como `production` en Nicalia → noindex activo en prod | CRÍTICO | Verificar `.env` en servidor Nicalia antes de go-live |
| Sitemap actual no incluye servicios ni zonas ES | ALTO | Regenerar sitemap antes de go-live |
| Snapshots con title genérico para reformas | MEDIO | Sin enlace = bajo riesgo de indexación involuntaria |
| og:image genérica en servicios y hubs | BAJO | Afecta solo compartición social, no posicionamiento |
| Hreflang x-default en snapshots apuntaba a `/` (sin idioma) | RESUELTO | El front-controller lo parchea en runtime |
| Hero images en `/assets/img/localidades/heroes/` existen para las 20 localidades | OK | Verificado en auditoría |

---

## 11. Checklist previo a producción (Nicalia)

### Técnico
- [ ] `APP_ENV=production` configurado en servidor
- [ ] Noindex NO aparece en versión producción (verificar con curl/browser)
- [ ] Sitemap actualizado con todas las URLs indexables
- [ ] Robots.txt correcto (sin bloqueos involuntarios)
- [ ] Formulario de contacto funciona en producción (SMTP configurado)
- [ ] WhatsApp FAB visible y funcional
- [ ] Popup de presupuesto funcional
- [ ] Redirección `/` → `/es/` activa (301)
- [ ] Canonical URLs apuntan al dominio de producción (`masqueclima.es`)
- [ ] Hreflang x-default apunta a `/es/` (parcheado en runtime)

### SEO
- [ ] Google Search Console verificado en nuevo dominio/subdominio
- [ ] Sitemap enviado a GSC
- [ ] No hay errores 404 en URLs indexadas previamente
- [ ] Mapa de redirecciones preparado si alguna URL cambia respecto a legacy
- [ ] Reformas NO aparecen en sitemap
- [ ] `og:image` accesible públicamente (`masqueclima.es/assets/img/og.jpg`)

### Contenido
- [ ] Ningún texto visible contiene "landing", "hub", "P1", "placeholder", "indexable"
- [ ] Guide cards muestran "Próximamente" (ya implementado)
- [ ] Sección guías indica claramente que están en preparación
- [ ] No hay certificaciones, direcciones ni datos falsos en el contenido

---

*Audit generado el 2026-05-28 en rama `fix/legacy-php-dev-stabilization`. Sin deploy, sin commit, sin cambios en producción/Nicalia, sin cambios en sitemap.xml, sin cambios en robots.txt.*

---

## 12. Expansión multiidioma — audit de validación (sesión posterior)

**Fecha:** sesión posterior al audit inicial  
**Alcance:** Hubs EN/DE/NL/RU/NO (servicios, zonas, guías) + 25 páginas de servicio en 5 idiomas + locality pages multilingüe.

### Qué se construyó

| Componente | Estado | Archivos |
|---|---|---|
| Hub content EN/DE/NL/RU/NO (3 hubs × 5 idiomas) | ✅ Creado | `app/content/hubs/{en,de,nl,ru,no}.php` |
| Servicio pages EN/DE/NL/RU/NO (5 servicios × 5 idiomas) | ✅ Creado/extendido | `app/content/services/{en,de,nl,ru,no}.php` |
| `localized_hub_url(lang, hub)` | ✅ Creado | `app/helpers.php` |
| `render_lang_hub_page()` + routing | ✅ Creado | `app/front_controller.php` |
| `render_service_detail_body($page, $lang)` | ✅ Creado con `$lang` param | `app/front_controller.php` |
| UI labels multiidioma en `$uiLabels` (6 langs) | ✅ Implementado | `app/front_controller.php` |
| Locality pill links por idioma (`lang_locality_pills_html()`) | ✅ Implementado | `app/front_controller.php` |
| `cta.quote` + `cta.whatsapp` en hub_hero.php | ✅ Corregido | `views/partials/hub_hero.php` |
| `views/service_detail.php` sin castellano hardcoded | ✅ Corregido | `views/service_detail.php` |

### Bugs encontrados y corregidos

| # | Bug | Archivo | Corrección |
|---|---|---|---|
| B-1 | `render_service_detail_body()` no pasaba `$lang` → UI siempre en castellano | `front_controller.php` | Añadido parámetro `$lang`, computed `$uiLabels` |
| B-2 | `localized_hub_url('services', $lang)` — orden de args invertido → NULL | `front_controller.php` | Corregido: `localized_hub_url($lang, 'services')` |
| B-3 | `hub_hero.php` con CTAs hardcoded en castellano visible en todos los idiomas | `views/partials/hub_hero.php` | Reemplazado con `t('cta.quote')` / `t('cta.whatsapp')` |
| B-4 | `lang_hub_guides_body()` renderizaba `guide_cards.php` con 6 cards hardcoded en castellano | `front_controller.php` | Eliminado el render de guide_cards para hubs no-ES |
| B-5 | `views/service_detail.php` con 14 strings hardcoded en castellano | `views/service_detail.php` | Todos reemplazados por variables del `$serviceUi` array |

### Falsos positivos descartados

- `<!-- MODAL: Presupuesto -->` — comentario HTML invisible.
- `id="inicio"` — atributo ID no visible.
- `href="/en/#contacto"` — anchor en nav; texto visible en inglés.

### Validación completada

| Validación | Resultado |
|---|---|
| PHP lint (todos los archivos) | ✅ 0 errores |
| BOM check | ✅ 0 archivos con BOM |
| 15 hub pages (5 langs × 3 hubs) | ✅ 15/15 — HTTP 200, canónica correcta, CTA localizado |
| 25 service pages (5 langs × 5 servicios) | ✅ 25/25 — HTTP 200, CLEAN (sin castellano), Svc:OK, FAQ:OK |
| Locality snapshots multilingüe | ✅ 21 snapshots × 5 idiomas — HTTP 200 spot-tested |
| Internal links desde hubs | ✅ Service cards → service pages; locality pills → snapshots; nav links correctos |
| Spanish leaks (grep en responses) | ✅ NINGUNO en respuestas no-ES tras correcciones |

### URLs de hub por idioma — referencia

| Hub | ES | EN | DE | NL | RU | NO |
|---|---|---|---|---|---|---|
| Servicios | `/es/servicios/` | `/en/services/` | `/de/dienstleistungen/` | `/nl/diensten/` | `/ru/uslugi/` | `/no/tjenester/` |
| Zonas | `/es/zonas/` | `/en/areas/` | `/de/gebiete/` | `/nl/gebieden/` | `/ru/raiony/` | `/no/omrader/` |
| Guías | `/es/blog/` | `/en/guides/` | `/de/ratgeber/` | `/nl/gidsen/` | `/ru/gidy/` | `/no/guider/` |

### Locality prefix por idioma

| Lang | Prefijo URL |
|---|---|
| `es` | `/es/aire-acondicionado-` |
| `en` | `/en/air-conditioning-` |
| `de` | `/de/klimaanlage-` |
| `nl` | `/nl/airco-` |
| `ru` | `/ru/konditsioner-` |
| `no` | `/no/aircondition-` |

### Qué queda pendiente (NO implementado, NO en scope)

| Pendiente | Motivo |
|---|---|
| Hreflang entre páginas equivalentes (hubs/servicios multiidioma) | Requiere decisión: ¿son realmente equivalentes o mercados separados? |
| Sitemap actualizado con nuevas URLs multiidioma | Fuera de scope — no tocar sitemap |
| Locality landings multiidioma con contenido patched (title/meta únicos) | Snapshots ya existen; el patching multilingüe es fase posterior |
| Guide cards en idiomas (en hub de guías no-ES) | Guías "Próximamente" — se eliminaron cards en castellano; se muestra solo texto descriptivo del hub |
| OG images específicas por idioma/hub | Requiere assets gráficos |

### git diff --stat (estado en el momento del audit)

```
app/content/services/de.php      | 251 +++
app/content/services/en.php      | 250 +++
app/content/services/nl.php      | 250 +++
app/content/services/ru.php      | 250 +++
app/front_controller.php         | 797 +++
app/helpers.php                  |  29 +-
app/translations/de.php          |   1 +
app/translations/en.php          |   3 +
app/translations/nl.php          |   1 +
app/translations/no.php          |   3 +
app/translations/ru.php          |   1 +
views/partials/guide_cards.php   |   9 +
views/partials/hub_hero.php      |   4 +-
views/service_detail.php         |  76 +++-
14 files changed, 1888 insertions(+), 37 deletions(-)

Untracked (nuevos):
  app/content/hubs/de.php
  app/content/hubs/en.php
  app/content/hubs/nl.php
  app/content/hubs/no.php
  app/content/hubs/ru.php
  app/content/services/no.php
```

## Correccion post-audit - 2026-05-28

Se corrigen regresiones visuales/UX detectadas en dev sin modificar produccion, sitemap, robots, DNS, `.env`, assets ni snapshots:

- Selector de idioma contextual para hubs, servicios y landings locales de aire acondicionado.
- Footer final por idioma reducido a 3 enlaces: servicios, zonas/areas y guias.
- CTA final localizado en hubs y paginas de servicio multiidioma.
- Cards de servicios no-ES alineadas con el patron visual ES (`hub-card`).
- Homes no-ES con accesos claros a servicios, zonas/areas y guias en el cuerpo.
- Modal de presupuesto validado/localizado por idioma, manteniendo formulario, CSRF, honeypot y POST.

Validaciones locales: 25/25 URLs no-ES PASS, selector contextual 20/20 PASS, smoke ES/NO 9/9 PASS, sin warnings PHP.

*Audit de expansión multiidioma — sin deploy, sin commit, sin producción/Nicalia, sin sitemap, sin robots.*

## Auditoria final predeploy - 2026-05-28

Referencia completa: `docs/FINAL_PREDEPLOY_AUDIT.md`.

Correcciones aplicadas durante la auditoria final:

- Hreflang contextual anadido a hubs y servicios dinamicos. Revalidacion: 48 URLs de hubs/servicios con `es`, `en`, `de`, `nl`, `ru`, `no` y `x-default`.
- Banner de cookies heredado localizado en EN/DE/NL/RU/NO.

Estado SEO final:

- PHP lint completo OK.
- BOM OK.
- 75 URLs principales auditadas sin errores HTTP en rutas canonicas.
- 120 landings locales multiidioma auditadas con 0 errores.
- JSON-LD decodifica en la muestra critica.

Pendientes antes de Nicalia:

- P1 corregido: `/politica-de-cookies` esta activa con 200 OK y canonical `https://masqueclima.es/politica-de-cookies`.
- Pendiente legal: revisar el contenido de `/politica-de-cookies` antes de produccion/Nicalia.
- P2: sitemap desfasado; faltan hubs y servicios multiidioma. No se modifico por instruccion.
