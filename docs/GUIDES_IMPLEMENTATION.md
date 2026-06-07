# Guides Implementation

## Scope
- Added and validated 18 localized guide URLs across ES, EN, DE, NL, RU and NO.
- Fixed localized internal links in guide content where legacy URLs were still present.
- Restored the guides hub so it renders non-empty localized cards in all six languages.
- Added explicit service-to-guide interlinking on localized service pages.

## Validated guide URLs
- ES: 3 guides under `/es/blog/`
- EN: 3 guides under `/en/guides/`
- DE: 3 guides under `/de/ratgeber/`
- NL: 3 guides under `/nl/gidsen/`
- RU: 3 guides under `/ru/gidy/`
- NO: 3 guides under `/no/guider/`

## Notable fixes
- Corrected broken DE service URLs inside guide content.
- Replaced the blank guides hub state with localized guide cards.
- Removed reliance on the missing `cta.read` translation key in the guide cards partial.
- Added localized guide links to service detail pages.

## Current status
- Guide pages: validated 18/18.
- Guides hubs: validated 6/6.
- Service-to-guide interlinking: validated 30/30.
- Sitemap: 18/18 guide detail URLs added and validated.
- Robots: validated unchanged and indexable.
- Turnstile: untouched.

## Sitemap closure (2026-05-29)
- Added 18 guide detail URLs to `public/sitemap.xml` with production domain entries.
- Validation result: XML valid, no duplicated URLs, no localhost/dev URLs, no missing trailing slash.
- Runtime check result: 18/18 guide routes return HTTP 200 in local validation.

## UX navigation update (2026-05-29)
- Language switch in guide pages is now contextual: switching language keeps users on the equivalent guide path, not the language home.
- Guides hubs now render only the 3 real guides in each language.
- Future placeholder cards were removed from guide hubs.
- `Próximamente` / `Coming soon` variants and `cta.read` were removed from guides hubs.
- Header menu structure is now aligned with ES in all languages (same count/order/structure; localized labels and localized URLs).

## Final SEO readiness reference
- Final production-readiness SEO verdict and risk matrix are documented in `docs/FINAL_SEO_PRODUCTION_READINESS_AUDIT.md`.

---

## Blog SEO P1 ES implementation - 2026-06-07

Scope of this implementation pass:
- Added the 6 prioritized ES commercial-intent guides to `app/content/guides/es.php`.
- Kept the remaining 6 editorial opportunities as planned/pending, not published.
- Updated `views/guide_detail.php` with optional quick summary, guide TOC, comparison tables, callouts, subheadings and a soft mid-page CTA.
- Updated `views/partials/guide_cards.php` so the ES guide hub shows the 6 P1 guides first and uses guide summaries correctly.
- Added temporary ES-only hreflang handling: new ES guides emit no alternate links until contextual EN/DE/NL/RU/NO versions exist and are validated.
- Added guide UI styles in `views/partials/hub_styles.php`.
- No sitemap, robots, DNS, Nginx, SMTP, Turnstile, deploy or production changes.

Implemented ES guide URLs:
- `/es/blog/cuanto-cuesta-instalar-aire-acondicionado-benidorm/`
- `/es/blog/por-que-aire-acondicionado-no-enfria/`
- `/es/blog/aire-acondicionado-conductos-o-split/`
- `/es/blog/aire-acondicionado-apartamentos-turisticos-benidorm/`
- `/es/blog/reparar-o-cambiar-aire-acondicionado/`
- `/es/blog/como-ahorrar-luz-aire-acondicionado/`

Local validation on `127.0.0.1:8787`:
- 6/6 new guide URLs return HTTP 200.
- Editorial body length is within the target range for the P1 batch: 901-1049 words per guide.
- 6/6 have absolute canonical URLs, unique titles, unique meta descriptions and exactly one H1.
- 6/6 have `BlogPosting`, visible FAQ content and `FAQPage` JSON-LD.
- 6/6 have quick summary, guide TOC, soft mid-page CTA, Google reviews before the final CTA and the final budget CTA.
- 6/6 emit 0 hreflang alternates while marked `es_only`.
- Internal links from the new guides return 200, including service detail pages, `/es/servicios/`, locality pages and `/es/zonas/`.
- `/es/blog/` returns 200 and contains the six new guide cards, with the first three cards matching the P1 order: cost in Benidorm, no-cooling diagnosis, conductos vs split.

Sitemap gate:
- Do not add the six new URLs to `public/sitemap.xml` until the user authorizes sitemap changes after final URL validation.
- Do not add EN/DE/NL/RU/NO guide records or guide hreflang map entries until each language has contextual content and natural slugs validated.

## Blog SEO expansion plan - 2026-06-07

Historical planning scope before the P1 ES implementation above:
- Audit current guide architecture and SEO behaviour.
- Define the first 12 ES guide opportunities.
- Prioritize the first 6 commercial-intent guides.
- Planning originally kept implementation gated; this is now superseded by the P1 ES implementation section above, where the six prioritized ES guides are present in local code.
- No sitemap, robots, DNS, Nginx, SMTP, Turnstile, deploy or production changes.

### Current guide audit

| Area | Current state | Gap before expansion |
| --- | --- | --- |
| Guide data | 3 guide records per language in `app/content/guides/{lang}.php` | Add new guide records only after ES slugs/content are approved |
| ES hub | `/es/blog/` is defined in `app/front_controller.php` | Keep route; do not duplicate in `app/content/hubs/es.php` |
| Non-ES hubs | `/en/guides/`, `/de/ratgeber/`, `/nl/gidsen/`, `/ru/gidy/`, `/no/guider/` in `app/content/hubs/{lang}.php` | Adapt after ES validation, not literal translation |
| Detail template | `views/guide_detail.php` renders hero, intro, sections, FAQ, related services and related areas | Add optional TOC, soft mid-CTA and stronger link groups before publishing new batch |
| Hub cards | `views/partials/guide_cards.php` renders the first 3 guides per language | Card summary currently expects `content[0]`; current guide records use `intro`, so summaries are not shown |
| Hreflang | `guide_hreflang_map()` and `localized_guide_equivalent_paths()` cover the 3 current guide keys | Add each new guide key only when all contextual language URLs are approved |
| Canonical | Absolute canonical generated as `https://masqueclima.es{path}` | OK; validate for every new guide |
| JSON-LD | Guide details get `BlogPosting`, visible FAQ gets `FAQPage`, and breadcrumb schema is added | OK; keep FAQ visible if FAQPage is emitted |
| Interlinking | Current guides link to service detail pages and 5 localities | New requirement: every guide must also link to services hub, zones hub and CTA/form |
| Social proof and CTA | Google reviews are injected before final budget CTA in dynamic guide shells | OK; keep this order |
| Sitemap | Repo sitemap currently includes guide hubs and the 18 existing guide details | Do not add new URLs until ES content and hreflang are validated |

### Current routes

| Lang | Hub | Current detail routes |
| --- | --- | --- |
| ES | `/es/blog/` | `/es/blog/aerotermia-bomba-calor-cuando-merece-la-pena/`, `/es/blog/que-potencia-aire-acondicionado-necesita-vivienda/`, `/es/blog/mantenimiento-aire-acondicionado-antes-verano/` |
| EN | `/en/guides/` | `/en/guides/heat-pump-aerothermal-when-worth-it/`, `/en/guides/what-air-conditioning-capacity-home-needs/`, `/en/guides/air-conditioning-maintenance-before-summer/` |
| DE | `/de/ratgeber/` | `/de/ratgeber/waermepumpe-aerothermie-wann-lohnt-es-sich/`, `/de/ratgeber/welche-klimaanlagen-leistung-wohnung-benoetigt/`, `/de/ratgeber/klimaanlagen-wartung-vor-dem-sommer/` |
| NL | `/nl/gidsen/` | `/nl/gidsen/warmtepomp-aerothermie-wanneer-de-moeite-waard/`, `/nl/gidsen/welk-vermogen-airco-woning-nodig/`, `/nl/gidsen/airco-onderhoud-voor-de-zomer/` |
| RU | `/ru/gidy/` | `/ru/gidy/teplovoj-nasos-aerotermiya-kogda-vygodno/`, `/ru/gidy/kakaya-moshchnost-konditsionera-nuzhna-dlya-doma/`, `/ru/gidy/obsluzhivanie-konditsionera-pered-letom/` |
| NO | `/no/guider/` | `/no/guider/varmepumpe-aerotermi-nar-lonner-det-seg/`, `/no/guider/hvilken-kapasitet-aircondition-trenger-bolig/`, `/no/guider/vedlikehold-aircondition-for-sommeren/` |

### Priority order

| Priority | Guide | Reason |
| --- | --- | --- |
| P1 | Cuanto cuesta instalar aire acondicionado en Benidorm | Price/quote intent; local and commercial |
| P1 | Por que mi aire acondicionado no enfria | Repair intent; urgent problem query |
| P1 | Aire acondicionado por conductos o split | Pre-purchase decision intent |
| P1 | Aire acondicionado para apartamentos turisticos en Benidorm | Owner/manager intent; high local fit |
| P1 | Reparar o cambiar el aire acondicionado | Repair vs replacement decision; quote intent |
| P1 | Como ahorrar luz con el aire acondicionado | Efficiency intent that can lead to maintenance/replacement |
| P2 | Que potencia de aire acondicionado necesito segun metros de vivienda | Existing guide; update to match new interlinking |
| P2 | Mantenimiento del aire acondicionado antes del verano | Existing guide; seasonal demand |
| P2 | Aerotermia y bomba de calor: cuando compensa | Existing guide; higher-ticket decision |
| P2 | Que revisar antes de instalar aire acondicionado en una vivienda | Installation preparation intent |
| P3 | Mejores marcas de aire acondicionado para viviendas en la Costa Blanca | Commercial research; must avoid unsupported rankings |
| P3 | Errores comunes al comprar aire acondicionado online | Buyer education; useful before install quote |

### Proposed ES guide metadata

| # | Status | Title SEO | Meta description | H1 | Slug ES | Short summary |
| --- | --- | --- | --- | --- | --- | --- |
| 1 | Existing/update | Que potencia de aire acondicionado necesito segun metros de vivienda | Guia para estimar la potencia de aire acondicionado segun metros, orientacion, aislamiento y uso real de la vivienda en la Costa Blanca. | Que potencia de aire acondicionado necesito segun metros de vivienda | `que-potencia-aire-acondicionado-necesita-vivienda` | Explica por que los metros son solo una referencia y cuando hace falta visita tecnica. |
| 2 | New P1 | Aire acondicionado por conductos o split: cual elegir | Comparamos conductos y split para viviendas en Benidorm y Costa Blanca: obra, confort, coste, mantenimiento y cuando conviene cada sistema. | Aire acondicionado por conductos o split: cual elegir | `aire-acondicionado-conductos-o-split` | Ayuda a elegir sistema antes de pedir presupuesto. |
| 3 | New P1 | Cuanto cuesta instalar aire acondicionado en Benidorm | Factores que influyen en el precio de instalar aire acondicionado en Benidorm: tipo de equipo, ubicacion, vivienda, preinstalacion y visita tecnica. | Cuanto cuesta instalar aire acondicionado en Benidorm | `cuanto-cuesta-instalar-aire-acondicionado-benidorm` | No publica precios inventados; orienta sobre variables reales de presupuesto. |
| 4 | New P3 | Mejores marcas de aire acondicionado para viviendas en la Costa Blanca | Como elegir marcas de aire acondicionado para viviendas en Costa Blanca segun uso, garantia, eficiencia, ruido, recambios y servicio tecnico. | Mejores marcas de aire acondicionado para viviendas en la Costa Blanca | `mejores-marcas-aire-acondicionado-costa-blanca` | Comparativa prudente sin ranking dudoso ni promesas absolutas. |
| 5 | Existing/update | Mantenimiento del aire acondicionado antes del verano | Que revisar antes del calor: filtros, desague, unidad exterior, ruidos, olores y cuando conviene llamar a un tecnico en Benidorm y Marina Baixa. | Mantenimiento del aire acondicionado antes del verano | `mantenimiento-aire-acondicionado-antes-verano` | Refuerza la guia existente con CTA preventivo y enlaces de hubs. |
| 6 | New P1 | Por que mi aire acondicionado no enfria | Causas habituales de un aire acondicionado que no enfria: filtros, gas, sonda, compresor, instalacion y senales para pedir revision. | Por que mi aire acondicionado no enfria | `por-que-aire-acondicionado-no-enfria` | Diagnostico claro para captar reparaciones sin animar manipulaciones peligrosas. |
| 7 | New P1 | Reparar o cambiar el aire acondicionado: cuando merece la pena | Criterios para decidir entre reparar o cambiar un aire acondicionado: edad, averia, consumo, confort, recambios y presupuesto. | Reparar o cambiar el aire acondicionado: cuando merece la pena | `reparar-o-cambiar-aire-acondicionado` | Ayuda a tomar una decision con llamada natural a diagnostico tecnico. |
| 8 | Existing/update | Aerotermia y bomba de calor: cuando compensa | Guia practica para saber cuando compensa la aerotermia o bomba de calor en Costa Blanca segun uso, vivienda, ACS y sistema de emision. | Aerotermia y bomba de calor: cuando compensa | `aerotermia-bomba-calor-cuando-merece-la-pena` | Mantener enfoque honesto y sin subvenciones/normativa sin fuente. |
| 9 | New P1 | Aire acondicionado para apartamentos turisticos en Benidorm | Que debe valorar un propietario antes de instalar aire acondicionado en un apartamento turistico: confort, ruido, control, mantenimiento y rapidez. | Aire acondicionado para apartamentos turisticos en Benidorm | `aire-acondicionado-apartamentos-turisticos-benidorm` | Contenido local para propietarios y gestores con CTA a visita/presupuesto. |
| 10 | New P3 | Errores comunes al comprar aire acondicionado online | Errores al comprar aire acondicionado online: potencia, instalacion, garantia, ubicacion de unidad exterior, ruido y servicio postventa. | Errores comunes al comprar aire acondicionado online | `errores-comprar-aire-acondicionado-online` | Educa antes de comprar y deriva a instalacion profesional. |
| 11 | New P1 | Como ahorrar luz con el aire acondicionado | Consejos realistas para ahorrar luz con el aire acondicionado: temperatura, modo, mantenimiento, aislamiento, equipo inverter y uso diario. | Como ahorrar luz con el aire acondicionado | `como-ahorrar-luz-aire-acondicionado` | Orienta a eficiencia sin cifras de ahorro no verificadas. |
| 12 | New P2 | Que revisar antes de instalar aire acondicionado en una vivienda | Checklist previo a instalar aire acondicionado: potencia, ubicacion, desague, unidad exterior, ruido, accesos, preinstalacion y presupuesto. | Que revisar antes de instalar aire acondicionado en una vivienda | `que-revisar-antes-instalar-aire-acondicionado-vivienda` | Checklist comercial que reduce dudas antes del formulario. |

### Structures, FAQs, links and CTA

#### 1. Que potencia de aire acondicionado necesito segun metros de vivienda
- H2/H3: Metros cuadrados como punto de partida; factores que cambian el calculo; ejemplos por tipo de estancia sin cifras cerradas; errores de sobredimensionar; cuando pedir visita tecnica.
- FAQ: Cuantas frigorias necesito para 25 m2?; Es mejor comprar mas potencia?; Influye la orientacion?; Que pasa si el equipo se queda corto?
- Internal links: `/es/servicios/instalacion-aire-acondicionado/`, `/es/servicios/`, `/es/zonas/`, `/es/aire-acondicionado-benidorm/`, `/es/aire-acondicionado-finestrat/`, final CTA.
- CTA: "Solicita un calculo de potencia con visita tecnica".
- Related service pages: instalacion, mantenimiento, aerotermia.
- Related localities: Benidorm, Finestrat, La Nucia, Altea.

#### 2. Aire acondicionado por conductos o split
- H2/H3: Diferencia basica; cuando conviene split; cuando convienen conductos; obra y preinstalacion; confort por zonas; mantenimiento y reparaciones; decision final.
- FAQ: Que es mas barato?; Los conductos consumen mas?; Puedo instalar conductos sin obra?; Que sistema encaja en apartamentos?
- Internal links: `/es/servicios/instalacion-aire-acondicionado/`, `/es/servicios/mantenimiento-climatizacion/`, `/es/servicios/`, `/es/zonas/`, `/es/aire-acondicionado-benidorm/`, `/es/aire-acondicionado-finestrat/`, final CTA.
- CTA: "Pide una visita para comparar conductos y split en tu vivienda".
- Related service pages: instalacion, mantenimiento.
- Related localities: Benidorm, Finestrat, La Nucia, Albir.

#### 3. Cuanto cuesta instalar aire acondicionado en Benidorm
- H2/H3: Por que no hay precio unico; factores del equipo; factores de la vivienda; unidad exterior y accesos; preinstalacion; que debe incluir un presupuesto; como evitar sorpresas.
- FAQ: Se puede dar precio por telefono?; Que encarece la instalacion?; Incluye retirada de equipo antiguo?; Cuando conviene visita tecnica?
- Internal links: `/es/servicios/instalacion-aire-acondicionado/`, `/es/servicios/`, `/es/zonas/`, `/es/aire-acondicionado-benidorm/`, `/es/aire-acondicionado-finestrat/`, `/es/aire-acondicionado-la-nucia/`, final CTA.
- CTA: "Pide presupuesto de instalacion en Benidorm".
- Related service pages: instalacion, mantenimiento, reparacion.
- Related localities: Benidorm, Finestrat, La Nucia, Albir.

#### 4. Mejores marcas de aire acondicionado para viviendas en la Costa Blanca
- H2/H3: Como entender "mejor marca"; eficiencia y ruido; disponibilidad de recambios; garantia y SAT; marcas segun uso de vivienda; que evitar al elegir solo por precio.
- FAQ: Hay una marca mejor para todos?; Importa mas la marca o la instalacion?; Que mirar en la ficha tecnica?; Conviene elegir por precio?
- Internal links: `/es/servicios/instalacion-aire-acondicionado/`, `/es/servicios/`, `/es/zonas/`, `/es/aire-acondicionado-benidorm/`, `/es/aire-acondicionado-altea/`, `/es/aire-acondicionado-calpe/`, final CTA.
- CTA: "Te recomendamos equipo segun vivienda, uso y presupuesto".
- Related service pages: instalacion, mantenimiento.
- Related localities: Benidorm, Altea, Calpe, Finestrat.

#### 5. Mantenimiento del aire acondicionado antes del verano
- H2/H3: Que puede revisar el usuario; filtros y olores; desague y condensados; unidad exterior en zonas costeras; senales de averia; apartamentos turisticos; cuando reservar mantenimiento.
- FAQ: Cada cuanto limpiar filtros?; Es normal que huela al encender?; Hace falta cargar gas todos los anos?; Que pasa si gotea agua?
- Internal links: `/es/servicios/mantenimiento-climatizacion/`, `/es/servicios/reparacion-aire-acondicionado/`, `/es/servicios/`, `/es/zonas/`, `/es/aire-acondicionado-benidorm/`, `/es/aire-acondicionado-calpe/`, `/es/aire-acondicionado-villajoyosa/`, final CTA.
- CTA: "Reserva mantenimiento antes de los meses de mas calor".
- Related service pages: mantenimiento, reparacion.
- Related localities: Benidorm, Calpe, Villajoyosa, Altea.

#### 6. Por que mi aire acondicionado no enfria
- H2/H3: Comprobaciones seguras del usuario; filtros; modo/temperatura/mando; unidad exterior; perdida de gas o fuga; averias electricas o compresor; cuando apagar y llamar.
- FAQ: Puede ser falta de gas?; Puedo limpiar filtros yo?; Por que sale aire pero no frio?; Cuanto tarda un diagnostico?
- Internal links: `/es/servicios/reparacion-aire-acondicionado/`, `/es/servicios/mantenimiento-climatizacion/`, `/es/servicios/`, `/es/zonas/`, `/es/aire-acondicionado-benidorm/`, `/es/aire-acondicionado-finestrat/`, final CTA.
- CTA: "Solicita revision si el equipo no enfria".
- Related service pages: reparacion, mantenimiento, instalacion.
- Related localities: Benidorm, Finestrat, Altea, Calpe.

#### 7. Reparar o cambiar el aire acondicionado
- H2/H3: Edad del equipo; tipo y coste de averia sin rangos inventados; consumo y confort; disponibilidad de piezas; ruido y fugas repetidas; cuando cambiar; cuando reparar.
- FAQ: Merece la pena reparar un equipo antiguo?; Si pierde gas siempre hay que cambiar?; Un equipo nuevo consume menos?; Puede revisarse antes de decidir?
- Internal links: `/es/servicios/reparacion-aire-acondicionado/`, `/es/servicios/instalacion-aire-acondicionado/`, `/es/servicios/`, `/es/zonas/`, `/es/aire-acondicionado-benidorm/`, `/es/aire-acondicionado-la-nucia/`, final CTA.
- CTA: "Pide diagnostico para decidir con datos".
- Related service pages: reparacion, instalacion, mantenimiento.
- Related localities: Benidorm, La Nucia, Finestrat, Altea.

#### 8. Aerotermia y bomba de calor: cuando compensa
- H2/H3: Que es una bomba de calor; cuando compensa en Costa Blanca; vivienda habitual vs uso estacional; ACS y emisores; alternativas split/multisplit; visita tecnica.
- FAQ: Sirve para agua caliente?; Compensa en apartamentos?; Necesito suelo radiante?; Es lo mismo que aire acondicionado?
- Internal links: `/es/servicios/aerotermia-bomba-calor/`, `/es/servicios/instalacion-aire-acondicionado/`, `/es/servicios/`, `/es/zonas/`, `/es/aire-acondicionado-benidorm/`, `/es/aire-acondicionado-altea/`, final CTA.
- CTA: "Estudiamos si la bomba de calor encaja en tu vivienda".
- Related service pages: aerotermia, instalacion, mantenimiento.
- Related localities: Benidorm, Altea, Finestrat, La Nucia.

#### 9. Aire acondicionado para apartamentos turisticos en Benidorm
- H2/H3: Necesidades de un apartamento turistico; confort y ruido; equipos resistentes y faciles de usar; mantenimiento entre reservas; rapidez de reparacion; control de consumo; presupuesto.
- FAQ: Que sistema conviene para alquiler turistico?; Como evitar quejas por ruido?; Cuando hacer mantenimiento?; Que pasa si falla en temporada alta?
- Internal links: `/es/servicios/instalacion-aire-acondicionado/`, `/es/servicios/mantenimiento-climatizacion/`, `/es/servicios/reparacion-aire-acondicionado/`, `/es/servicios/`, `/es/zonas/`, `/es/aire-acondicionado-benidorm/`, `/es/aire-acondicionado-finestrat/`, final CTA.
- CTA: "Pide presupuesto para apartamento turistico en Benidorm".
- Related service pages: instalacion, mantenimiento, reparacion.
- Related localities: Benidorm, Finestrat, Albir, Villajoyosa.

#### 10. Errores comunes al comprar aire acondicionado online
- H2/H3: Comprar solo por precio; potencia mal elegida; instalacion no incluida; garantia y servicio tecnico; ubicacion exterior; desague y ruido; que consultar antes de comprar.
- FAQ: Puedo comprar el equipo y pedir solo instalacion?; Que pasa si la potencia no es correcta?; La garantia cubre mala instalacion?; Que mirar antes de pagar online?
- Internal links: `/es/servicios/instalacion-aire-acondicionado/`, `/es/servicios/reparacion-aire-acondicionado/`, `/es/servicios/`, `/es/zonas/`, `/es/aire-acondicionado-benidorm/`, `/es/aire-acondicionado-calpe/`, final CTA.
- CTA: "Consulta antes de comprar para evitar errores de instalacion".
- Related service pages: instalacion, reparacion.
- Related localities: Benidorm, Calpe, Altea, Finestrat.

#### 11. Como ahorrar luz con el aire acondicionado
- H2/H3: Temperatura de consigna; modo automatico/inverter; limpieza de filtros; persianas y aislamiento; horarios y uso inteligente; cuando un equipo antiguo dispara consumo; mantenimiento.
- FAQ: Que temperatura ahorra mas?; Apagar y encender consume mas?; Los filtros influyen en el consumo?; Cuando cambiar un equipo antiguo?
- Internal links: `/es/servicios/mantenimiento-climatizacion/`, `/es/servicios/instalacion-aire-acondicionado/`, `/es/servicios/`, `/es/zonas/`, `/es/aire-acondicionado-benidorm/`, `/es/aire-acondicionado-la-nucia/`, final CTA.
- CTA: "Revisamos si tu equipo esta consumiendo de mas".
- Related service pages: mantenimiento, instalacion, reparacion.
- Related localities: Benidorm, La Nucia, Finestrat, Altea.

#### 12. Que revisar antes de instalar aire acondicionado en una vivienda
- H2/H3: Superficie y distribucion; ubicacion interior; unidad exterior; desague; ruido y comunidad; preinstalacion; acceso de montaje; presupuesto y garantias.
- FAQ: Que mira el tecnico en una visita?; Se puede instalar sin preinstalacion?; Donde poner la unidad exterior?; Que debe incluir el presupuesto?
- Internal links: `/es/servicios/instalacion-aire-acondicionado/`, `/es/servicios/mantenimiento-climatizacion/`, `/es/servicios/`, `/es/zonas/`, `/es/aire-acondicionado-benidorm/`, `/es/aire-acondicionado-finestrat/`, final CTA.
- CTA: "Pide una visita previa antes de elegir equipo".
- Related service pages: instalacion, mantenimiento.
- Related localities: Benidorm, Finestrat, La Nucia, Altea.

### Multi-language phase

Do not add EN/DE/NL/RU/NO records until ES is validated. After ES approval:
- Adapt search intent by language and audience, especially for foreign homeowners and second-home owners.
- Keep natural slugs by language.
- Add guide keys in both `localized_guide_equivalent_paths()` and `guide_hreflang_map()` only when each language URL has a real page.
- Validate contextual language switch before sitemap.

### Files to touch in the implementation phase

| File | Intended change |
| --- | --- |
| `app/content/guides/es.php` | Add approved ES guide records and update existing 3 where needed |
| `views/guide_detail.php` | Add optional TOC and mid-page CTA support; optionally render services/zones hubs as required link groups |
| `views/partials/guide_cards.php` | Use `intro`/`summary` for card descriptions and show more than 3 cards if approved |
| `app/helpers.php` | Add new guide equivalents after multi-language URLs are approved |
| `app/front_controller.php` | Extend `guide_hreflang_map()` and any guide UI helpers for new keys |
| `public/assets/css/styles.css` | Style TOC, soft CTA and any guide-specific premium blocks |
| `public/sitemap.xml` | Only after final URL validation and explicit authorization |

### SEO validation checklist before sitemap

- Canonical absolute for every guide.
- Full contextual hreflang: ES, EN, DE, NL, RU, NO, x-default.
- Unique title and meta description.
- One visible H1.
- Visible FAQ if `FAQPage` is emitted.
- No duplicate or placeholder guide content.
- No orphan URLs: each guide linked from hub and at least one relevant service page.
- Each guide links to a main related service, at least two localities, `/es/servicios/`, `/es/zonas/` and a CTA/form.
- Google reviews remain before final CTA.
- No 404s in guide links.
- Sitemap updated only after all previous checks pass.
