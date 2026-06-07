# Final Predeploy Audit - Masqueclima

Fecha: 2026-05-28
Rama: `fix/legacy-php-dev-stabilization`
Entorno auditado: local PHP built-in server en `localhost:8788`
Alcance: auditoria final previa a commit/revision dev. No produccion, no Nicalia, no DNS.

## 1. Resumen ejecutivo

La rama esta coherente a nivel de routing principal, PHP, encoding, metadatos, selector contextual, servicios multiidioma, hubs multiidioma, localidades y assets. Se corrigieron dos bugs seguros durante la auditoria:

- Hreflang ausente en hubs y servicios renderizados dinamicamente.
- Banner de cookies heredado en espanol en paginas no-ES.
- P1 `/politica-de-cookies` enlazado desde el banner y anteriormente en 404.

No hay P0/P1 tecnicos abiertos tras activar `/politica-de-cookies`. La pagina legal es prudente y debe revisarse legalmente antes de produccion/Nicalia.

Veredicto: apto para commit/revision dev. No apto para subida final a Nicalia hasta revisar legalmente la politica de cookies y actualizar sitemap segun alcance aprobado.

Addendum 2026-05-29: ver `docs/FINAL_SEO_PRODUCTION_READINESS_AUDIT.md` para el dictamen final de readiness SEO, validación de no-regresión sobre home legacy y checklist mínimo previo a dominio bueno.

Addendum 2026-05-29 (cierre P2 sitemap):

- Se añadieron las 18 URLs de detalle de guías al sitemap productivo.
- Validación sitemap: XML válido, sin `dev.masqueclima.es`, sin `localhost`, sin duplicados, sin URLs sin slash final.
- Validación runtime local: 18/18 guías en 200.
- `public/robots.txt` permanece indexable y sin cambios funcionales.
- Riesgo noindex en producción: controlado por `APP_ENV`; valor esperado para dominio bueno: `APP_ENV=production`.

Checklist final antes de dominio bueno (sin deploy automático):

1. Confirmar `APP_ENV=production` en runtime objetivo.
2. Confirmar `robots.txt` indexable (`Allow: /`) y sitemap productivo.
3. Confirmar ausencia de `X-Robots-Tag: noindex` en edge/CDN/proxy.
4. Confirmar 200 + canonical + hreflang en `/es/`, `/es/blog/` y una guía por idioma.
5. Si Turnstile se activa en producción, validar claves reales en entorno y flujo de formulario tras deploy.
6. Limpiar cache/CDN si aplica para publicar sitemap/metadatos actualizados.

Addendum 2026-05-30 (módulo reseñas Google):

- Añadido módulo global de reseñas Google antes del CTA final en páginas comerciales.
- Fuente manual en `app/content/google_reviews.php` con enlace externo oficial del perfil, rating 5,0, 70 opiniones y 12 reseñas iniciales aportadas por usuario.
- Sin API, sin iframe y sin widget externo pesado.
- Sin fotos de usuarios: el módulo usa avatares sobrios con iniciales.
- Sin cambios en `robots.txt`, `sitemap.xml`, DNS, SMTP o Turnstile.
- Sin marcado `Review/AggregateRating` en JSON-LD por política de riesgo SEO.

## 2. Estado git

Estado inicial de Fase 0:

```text
## fix/legacy-php-dev-stabilization...origin/main
git diff --stat: vacio
git diff --name-only: vacio
git ls-files --others --exclude-standard: vacio
```

Ultimos commits:

```text
015b0da feat: complete multilingual hubs and service UX
cc87f76 feat: complete multilingual hubs and service UX
32d8f87 feat: add multilingual hubs and service pages
f514ed7 feat: add locality hero images
643aa4e fix: improve service detail image block
```

Cambios hechos durante esta auditoria:

- `app/helpers.php`
- `app/front_controller.php`
- `docs/FINAL_PREDEPLOY_AUDIT.md`
- `docs/SEO_MASTER_AUDIT.md`
- `docs/MULTILINGUAL_SEO_EXPANSION_PLAN.md`
- `docs/INTERNAL_LINKING_PLAN.md`

No se hizo commit durante la auditoria original; estos cambios quedaron para el cierre post-commit.

## 3. URLs auditadas

Rutas base:

```text
/
/es/
/en/
/de/
/nl/
/ru/
/no/
```

Hubs:

```text
/es/servicios/
/es/zonas/
/es/blog/
/en/services/
/en/areas/
/en/guides/
/de/dienstleistungen/
/de/gebiete/
/de/ratgeber/
/nl/diensten/
/nl/gebieden/
/nl/gidsen/
/ru/uslugi/
/ru/raiony/
/ru/gidy/
/no/tjenester/
/no/omrader/
/no/guider/
```

Servicios multiidioma: 30 URLs auditadas, 5 servicios por idioma en ES/EN/DE/NL/RU/NO.

Landings locales:

- 20 landings ES del inventario principal auditadas.
- 120 landings localizadas auditadas adicionalmente: 20 municipios x 6 idiomas. Resultado: 0 errores.

## 4. Resultado de lint

PHP lint completo en `app`, `views`, `public`, `public_html`:

```text
PHP LINT OK: 0 errores
```

## 5. Resultado de crawl

Crawl principal: 75 URLs.

Resultado:

- HTTP 200 en rutas auditadas con slash.
- `/` redirige 301 a `/es/` cuando se comprueba sin seguir redirects.
- `<title>` presente.
- Meta description presente.
- Canonical absoluto presente.
- Canonical sin `dev.masqueclima.es`.
- H1 unico.
- `html lang` correcto.
- Sin warnings/notices/fatals PHP.
- Sin `localhost`, `127.0.0.1`, `dev.masqueclima.es`, `var_dump` o `print_r` en HTML renderizado.

## 6. Actualizacion 2026-05-29

Nuevo cierre de guias dentro de la rama `fix/legacy-php-dev-stabilization`:

- 18/18 guias validadas con 200, canonical absoluto, 7 hreflang, og/twitter image y JSON-LD completo.
- Hubs de guias corregidos en los 6 idiomas: tarjetas localizadas visibles, CTA localizada y sin `cta.read`.
- Interlinking servicios -> guias validado en 30/30 paginas de servicio con enlace localizado esperado.
- `public/sitemap.xml` y `public/robots.txt` sin cambios en esta pasada.
- Scripts temporales de validacion eliminados tras el cierre.

Nota: detectores case-insensitive marcaban falsos positivos por `Metodo`/`swiper`; se verifico con busqueda case-sensitive.

## 6. Resultado de selector idioma

Casos obligatorios auditados y correctos:

- `/de/dienstleistungen/` enlaza a hubs equivalentes ES/EN/NL/RU/NO.
- `/no/tjenester/installasjon-av-aircondition/` enlaza a servicios equivalentes ES/EN/DE/NL/RU.
- `/nl/gebieden/` enlaza a zonas equivalentes ES/EN/DE/RU/NO.
- `/ru/gidy/` enlaza a guias equivalentes ES/EN/DE/NL/NO.
- `/es/aire-acondicionado-benidorm/` enlaza a landings equivalentes EN/DE/NL/RU/NO.

Todos los destinos obligatorios devuelven 200.

## 7. Resultado multiidioma

Se revisaron 45 paginas no-ES:

- Homes no-ES.
- Hubs no-ES.
- Servicios no-ES.

Resultado:

- 0 hits de frases visibles en castellano en el set obligatorio.
- Modal localizado por idioma.
- Banner de cookies corregido para EN/DE/NL/RU/NO.
- CTA final localizado.
- Footer con 3 enlaces principales por idioma.

Pendiente P2: `app/content/guides/en.php`, `de.php`, `nl.php`, `ru.php` contienen contenido guia en castellano no renderizado actualmente en los hubs auditados. No se corrigio porque no forma parte del HTML visible del crawl actual y podria requerir trabajo editorial.

## 8. Resultado SEO tecnico

Canonical:

- Absoluto y en dominio `https://masqueclima.es`.
- Sin dev/localhost.

Hreflang:

- Homes: 7 alternates (`es`, `en`, `de`, `nl`, `ru`, `no`, `x-default`).
- Landings locales: 7 alternates.
- Hubs y servicios: se detecto P1 por ausencia de alternates y se corrigio.
- Revalidacion: 48 hubs/servicios con 0 problemas de hreflang.

JSON-LD:

- URLs extraidas y decodificadas sin errores:
  - `/es/`
  - `/en/services/air-conditioning-installation/`
  - `/de/dienstleistungen/klimaanlage-installation/`
  - `/nl/diensten/airco-installatie/`
  - `/ru/uslugi/ustanovka-konditsionera/`
  - `/no/tjenester/installasjon-av-aircondition/`
  - `/es/aire-acondicionado-benidorm/`
  - `/es/aire-acondicionado-guadalest/`
- Servicios incluyen `Service`, `HVACBusiness`, `FAQPage`, `BreadcrumbList`.
- Localidades auditadas incluyen `FAQPage` visible.
- Home incluye Organization/HVACBusiness/WebSite/BreadcrumbList.

Imagen social:

- `og:image` y `twitter:image` accesibles localmente en muestra SEO: 200.

## 9. Resultado assets

Assets renderizados auditados en 75 URLs:

```text
Unique refs: 458
Broken local asset refs: 0
```

Comprobado:

- Logo existe.
- Flags ES/EN/DE/NL/RU/NO existen.
- Favicon existe en ruta usada por HTML: `/assets/img/favicon.ico`.
- Heroes de servicios/localidades referenciados sin rotura local.
- No rotura real en `Logo%20Panasonic.png`; existe como `Logo Panasonic.png` y se valido con URL decode.

## 10. Resultado enlaces internos

Fuente: 36 paginas principales.

```text
Unique internal links checked: 195
Bad internal links: 1 en auditoria original; `/politica-de-cookies` corregido posteriormente.

## 11. Actualización UX navegación (2026-05-29)

- Selector de idioma en guías corregido: ya no redirige a la home del idioma; mantiene la guía equivalente.
- Hubs de guías ajustados para mostrar solo 3 guías reales; sin placeholders de futuro.
- Menú/header unificado con estructura ES en todos los idiomas, sin reintroducir `Reformas` ni enlaces extra.
- `public/sitemap.xml` y `public/robots.txt` permanecen sin cambios.
```

Enlace roto detectado en auditoria original:

```text
/politica-de-cookies -> 404
```

Origen: banner de cookies en homes, hubs y servicios auditados.

Estado posterior:

- `/politica-de-cookies` devuelve 200 OK.
- `/es/politica-de-cookies/` devuelve 200 OK como alias seguro.
- Canonical: `https://masqueclima.es/politica-de-cookies`.
- Requiere revision legal final antes de produccion/Nicalia.

## 11. Resultado formulario/modal

Validado en homes, servicio y localidad:

- `quoteModal` presente.
- Labels/botones traducidos.
- `action="/contact-submit.php"`.
- CSRF presente.
- Honeypot presente.
- `return_to` contextual presente.
- `GET /contact-submit.php` y `HEAD /contact-submit.php` devuelven 405.
- Busqueda de secretos: solo nombres de variables/env (`SMTP_USER`, `SMTP_PASS`), sin credenciales hardcodeadas visibles.

No se hizo POST para no generar logs/contactos falsos.

## 12. Resultado robots/sitemap

Robots:

```text
User-agent: *
Allow: /
Sitemap: https://masqueclima.es/sitemap.xml
```

Sin `dev.masqueclima.es` ni `localhost`.

Sitemap:

- 126 URLs.
- Incluye homes y landings locales.
- No incluye hubs/servicios nuevos multiidioma.
- No se modifico por instruccion expresa.

**ACTUALIZADO 2026-05-28:** Sitemap regenerado con 174 URLs. Ver `docs/SITEMAP_HREFLANG_FINAL_AUDIT.md`.

## 13. Problemas P0/P1/P2

P0:

- Ninguno abierto.

P1:

- Ninguno abierto.

P2:

- ~~Sitemap desfasado: faltan hubs y servicios multiidioma.~~ **CERRADO** — sitemap actualizado a 174 URLs (6 homes + 18 hubs + 30 servicios + 120 localidades).
- ~~Páginas legales ausentes~~ **CERRADO** — 18 páginas legales implementadas (3 tipos × 6 idiomas). Ver `docs/LEGAL_PAGES_IMPLEMENTATION.md`.
- ~~Cookie banner con URL hardcodeada a /politica-de-cookies~~ **CERRADO** — banner usa URL localizada por idioma.
- ~~Footer sin enlaces legales~~ **CERRADO** — footer incluye aviso legal · privacidad · cookies por idioma.
- ~~`og:image` genérica en todas las páginas~~ **CERRADO Sesión 6** — og:image específica por localidad, hub y servicio (ver `docs/OG_IMAGE_REFINEMENT.md`).
- Variantes sin slash (`/es`, `/en`, `/es/servicios`) sirven 200 en PHP local con canonical a slash. Valorar redirect canonico antes de produccion si Nicalia no lo fuerza.
- Contenido de `app/content/guides/*` no-ES sigue en castellano aunque no este renderizado en hubs actuales.
- Comentario legacy largo en `app/content/services/de.php` con contenido antiguo en castellano. No renderiza.
- No se hizo revision visual con navegador real/Playwright; la validacion visual fue estructural por HTML/CSS.
- Datos identificativos del titular (NIF/CIF, denominación social, dirección) en páginas legales marcados como pendientes. **Deben completarse antes de Nicalia.**

---

## 14. Actualización sesión 3 — 2026-05-28

### Páginas legales

- **18 páginas legales** implementadas: 3 tipos (cookies, privacidad, aviso legal) × 6 idiomas.
- Arquitectura: `app/content/legal.php` + funciones de render en `front_controller.php`.
- `/politica-de-cookies/` → 301 → `/es/politica-de-cookies/` (SEO redirect a canónico).
- Hreflang completo en todas las páginas legales (7 alternates por página).
- **Responsable visible**: `DANIEL CUENCA MOYA` (en privacidad + avisos legales, 12 secciones × 6 idiomas).
- **Email legal**: `administracion@masqueclima.es` (privacidad + avisos legales, todas las lenguas).
- **Teléfono**: deliberadamente excluido del texto de páginas legales.
- **NIF/titular/domicilio**: no incluidos ni inventados. Sujeto a revisión legal antes de Nicalia (puede ser obligatorio por LSSI-CE).
- No hay placeholders visibles en el sitio. Los datos pendientes están documentados en `docs/LEGAL_PAGES_IMPLEMENTATION.md`.
- Validación HTTP: todas las URLs devuelven 200. Ver `docs/LEGAL_PAGES_IMPLEMENTATION.md`.

### Turnstile (anti-spam form)

- Cloudflare Turnstile integrado en el modal de presupuesto.
- **ACTIVADO en dev VPS** (Sesión 5+6) — claves reales en `/srv/apps/masqueclima-legacy-dev/shared/.env`, `TURNSTILE_ENABLED=true`.
- Test manual confirmado por usuario: `https://dev.masqueclima.es/es/?sent=1#inicio` → éxito ✅.
- Fallos de validación → `?sent=2` → error modal (mismo que CSRF).
- Ver `docs/TURNSTILE_RUNTIME_AUDIT.md` (Sección 8 — resultados de validación).

### Estado PHP lint sesión 3

```text
PHP LINT OK: 0 errores (app/, views/, public/, public_html/)
```

## 14. Cambios corregidos durante la auditoria

1. `app/helpers.php`
   - Nuevo helper `localized_hreflang_links_html()`.
   - Usa el mapa contextual existente (`localized_equivalent_url`) para construir alternates absolutos.

2. `app/front_controller.php`
   - Inserta hreflang contextual en hubs/servicios ES y multiidioma, incluyendo shells minimos.
   - Localiza banner de cookies heredado para EN/DE/NL/RU/NO.
   - Activa `/politica-de-cookies` y `/es/politica-de-cookies/` con canonical unico.

## 15. Pendientes antes de Nicalia

1. Revision legal final de `/politica-de-cookies`.
2. ~~Actualizar `public/sitemap.xml`~~ **CERRADO** — sitemap actualizado a 174 URLs.
3. Decidir si se fuerza redirect slash canonico.
4. Limpiar contenido no renderizado de guias multiidioma.
5. Ejecutar revision visual real en navegador contra el entorno dev final.
6. Validacion visual pre-Nicalia (formulario, modal, hero images, selector idioma).

## 16. Recomendacion final

**Apto para commit de `public/sitemap.xml` y docs.**

No apto para deploy final a Nicalia hasta revision visual y revision legal de `/politica-de-cookies`.

Confirmaciones:

- No commit.
- No deploy.
- No produccion/Nicalia.
- No DNS.
- No `.env`.
- No robots modificado.
- No sitemap modificado.
- No assets borrados.
- No snapshots borrados.
