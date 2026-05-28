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

Pendiente antes de Nicalia: regenerar o actualizar sitemap con hubs/servicios cuando se decida el alcance final.

## 13. Problemas P0/P1/P2

P0:

- Ninguno abierto.

P1:

- Ninguno abierto tras activar `/politica-de-cookies`.

P2:

- Sitemap desfasado: faltan hubs y servicios multiidioma.
- Variantes sin slash (`/es`, `/en`, `/es/servicios`) sirven 200 en PHP local con canonical a slash. Valorar redirect canonico antes de produccion si Nicalia no lo fuerza.
- Contenido de `app/content/guides/*` no-ES sigue en castellano aunque no este renderizado en hubs actuales.
- Comentario legacy largo en `app/content/services/de.php` con contenido antiguo en castellano. No renderiza.
- No se hizo revision visual con navegador real/Playwright; la validacion visual fue estructural por HTML/CSS.

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
2. Actualizar `public/sitemap.xml` cuando se apruebe incluir hubs/servicios.
3. Decidir si se fuerza redirect slash canonico.
4. Limpiar contenido no renderizado de guias multiidioma.
5. Ejecutar revision visual real en navegador contra el entorno dev final.

## 16. Recomendacion final

Apto para commit/revision dev de esta rama.

No apto para deploy final a Nicalia hasta revision legal de `/politica-de-cookies` y actualizacion de sitemap segun alcance aprobado.

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
