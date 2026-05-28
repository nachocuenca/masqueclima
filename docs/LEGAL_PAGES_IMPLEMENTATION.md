# Legal Pages — Implementation Notes

**Status:** Implemented (dev-validated)  
**Date:** 2026-05-28  
**Branch:** `fix/legacy-php-dev-stabilization`

---

## Overview

18 legal pages implemented across 3 types × 6 languages, rendered dynamically from the home snapshot base.

---

## URL Map

### Cookie Policy

| Lang | Path |
|------|------|
| es | `/es/politica-de-cookies/` |
| en | `/en/cookie-policy/` |
| de | `/de/cookie-richtlinie/` |
| nl | `/nl/cookiebeleid/` |
| ru | `/ru/cookie-policy/` |
| no | `/no/cookie-policy/` |

### Privacy Policy

| Lang | Path |
|------|------|
| es | `/es/politica-de-privacidad/` |
| en | `/en/privacy-policy/` |
| de | `/de/datenschutzerklaerung/` |
| nl | `/nl/privacybeleid/` |
| ru | `/ru/politika-konfidentsialnosti/` |
| no | `/no/personvernerklaering/` |

### Legal Notice

| Lang | Path |
|------|------|
| es | `/es/aviso-legal/` |
| en | `/en/legal-notice/` |
| de | `/de/impressum/` |
| nl | `/nl/juridische-mededeling/` |
| ru | `/ru/pravovoe-uvedomlenie/` |
| no | `/no/juridisk-varsel/` |

---

## Legacy Redirect

`/politica-de-cookies/` → **301** → `/es/politica-de-cookies/`  
(old banner links remain functional; SEO juice passes to the canonical ES path)

---

## Architecture

### Files modified / created

| File | Change |
|------|--------|
| `app/content/legal.php` | NEW — all 18 page configs (title, description, canonical, body HTML) |
| `app/front_controller.php` | Added routing, render functions, hreflang map, cookie banner URL update, footer legal links |
| `app/config.php` | No changes for legal (Turnstile config added separately) |

### Key functions in `front_controller.php`

| Function | Purpose |
|----------|---------|
| `legal_pages()` | Lazy-loads `app/content/legal.php`, static-cached |
| `legal_page_for_path(string $path)` | Returns page config array or null |
| `legal_hreflang_map()` | Returns all cross-lang URL maps for all 3 types |
| `legal_cookie_policy_url(string $lang)` | Returns cookie policy URL for given lang |
| `legal_hreflang_html(string $type)` | Generates `<link rel="alternate">` tags for a legal page type |
| `render_legal_page(string $path)` | Main render entry point; returns HTML or null |
| `render_legal_shell(...)` | Renders using lang home snapshot base; falls back to minimal shell |
| `render_legal_minimal_shell(...)` | Fallback if home snapshot unavailable |
| `patch_legal_head(...)` | Patches title, description, canonical, hreflang in the head |
| `footer_legal_links_html(string $lang)` | Returns per-lang legal footer row |

### Rendering pipeline

1. `render_legal_page($path)` → `legal_page_for_path($path)` → load `app/content/legal.php`
2. Find lang home snapshot (`/{lang}/` → `app/snapshots/{lang}.html`)
3. Extract head+header, interactive tail (WhatsApp FAB + quote modal), tail
4. Patch head (title, description, canonical, hreflang)
5. Assemble: `head+header + legal body + interactiveTail + tail`
6. `patch_snapshot_html()` runs on the result → CSRF, footer links, cookie banner, body links rewriting

---

## Hreflang

Each legal page injects full cross-lang hreflang + x-default pointing to ES:

```html
<!-- Hreflang -->
<link rel="alternate" hreflang="es" href="https://masqueclima.es/es/politica-de-cookies/">
<link rel="alternate" hreflang="en" href="https://masqueclima.es/en/cookie-policy/">
<link rel="alternate" hreflang="de" href="https://masqueclima.es/de/cookie-richtlinie/">
<link rel="alternate" hreflang="nl" href="https://masqueclima.es/nl/cookiebeleid/">
<link rel="alternate" hreflang="ru" href="https://masqueclima.es/ru/cookie-policy/">
<link rel="alternate" hreflang="no" href="https://masqueclima.es/no/cookie-policy/">
<link rel="alternate" hreflang="x-default" href="https://masqueclima.es/es/politica-de-cookies/">
```

---

## Cookie Banner URLs Updated

`patch_snapshot_cookie_banner()` now uses `legal_cookie_policy_url($lang)` instead of the hardcoded `/politica-de-cookies` URL. Each language gets the correct localised cookie policy link.

---

## Footer Legal Links

`footer_main_links_html()` now appends a second row below the nav links:

```
Servicios | Zonas | Guías
Aviso legal · Privacidad · Cookies
```

Rendered via `footer_legal_links_html(string $lang)`. Links are localised per language.

---

## Content Notes

### Responsable / titular visible

Responsable visible en páginas legales: **`DANIEL CUENCA MOYA`**

Aparece en:
- Privacidad (6 langs): sección "Responsable del tratamiento / Data controller / Verantwortlicher / etc."
- Aviso legal (6 langs): sección "Datos del titular / Site owner / Impressum / etc."

**No publicado** (dato disponible, pendiente de decisión legal antes de Nicalia):
- NIF: disponible pero no publicado
- Domicilio: disponible pero no publicado

Antes de producción/Nicalia, confirmar con asesoría legal si LSSI-CE exige publicar NIF y domicilio para esta actividad concreta.

### Email de contacto legal
Email usado en páginas legales: **`administracion@masqueclima.es`** (privacidad + avisos legales).
El email `info@masqueclima.es` se usa en el resto del sitio (formulario, WhatsApp, JSON-LD) pero NO en el texto de las páginas legales.

### Teléfono
El teléfono está **deliberadamente excluido** del texto visible de las páginas legales.
Aparece únicamente en: JSON-LD structured data (global), WhatsApp FAB (global), y modal de error de formulario. Ninguno forma parte del cuerpo de las páginas legales.

### NIF / titular / domicilio — Revisión legal pendiente
La LSSI-CE (Ley de Servicios de la Sociedad de la Información, España) **puede exigir** para sitios comerciales:
- Denominación social o nombre/apellidos del titular
- NIF del titular o autónomo
- Domicilio o establecimiento permanente

Estos datos **no se han incluido ni inventado**. El aviso legal muestra únicamente nombre comercial (`+QUECLIMA`), web (`masqueclima.es`) y email (`administracion@masqueclima.es`). No hay texto de "pendiente" visible en el sitio.

**Antes de Nicalia/producción**, el usuario debe confirmar con asesoría legal:
- ¿Es actividad de autónomo o empresa con CIF?
- ¿Debe publicarse NIF/CIF y domicilio por LSSI?
- Si aplica, aportar los datos reales para incluirlos.

### Contenido de las páginas
- Privacy: responsable simplificado (nombre comercial + email), datos recogidos por formulario (nombre/teléfono/email del usuario), finalidad, base jurídica, retención 12 meses, destinatarios (SMTP + GA4), transferencias internacionales (Google SCCs/EU-US DPF), derechos ARSOLPO, AEPD.
- Cookies: cookies técnicas/sesión/terceros, gestión de navegador.
- Aviso legal: nombre comercial, web, email, actividad, propiedad intelectual, exención responsabilidad, ley española + juzgados Alicante.

---

## Sitemap

Legal pages are **NOT included** in `public/sitemap.xml` (thin content / noindex candidate until identifiers are complete).

---

## Pre-Production Checklist

Before going live (Nicalia), the following must be confirmed:
- [ ] Confirm with legal counsel whether LSSI requires NIF/CIF + domicilio for this site type
- [ ] If required: provide full legal entity name, NIF/CIF and registered address to add to legal pages
- [ ] DPO contact (if applicable under GDPR)
- [ ] Final review by qualified legal counsel
- [ ] Sitemap: decide whether to add legal pages (currently excluded — thin content)

Datos que el usuario debe aportar si se requieren:
- Denominación social (o nombre + apellidos si autónomo)
- NIF / CIF
- Domicilio fiscal o establecimiento
