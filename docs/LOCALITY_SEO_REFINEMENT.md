# LOCALITY_SEO_REFINEMENT.md

## Problem: F3 — Non-ES locality pages had generic title/meta

All 100 locality landing pages in EN/DE/NL/RU/NO (20 slugs × 5 non-ES languages)
were serving the home page title and meta description instead of locality-specific content.

**Root cause:** `patch_es_p1_location_page()` in `app/front_controller.php` guarded with
`if ($lang !== 'es') return $html;`, leaving EN/DE/NL/RU/NO locality snapshots unpatched.

**H1 was already correct** in all 120 snapshots (e.g. "Air conditioning in Benidorm" in EN).

---

## Solution: runtime patch `patch_nonES_locality_seo()`

Added two new functions to `app/front_controller.php`:

### `locality_display_names(): array`
Maps the 20 locality slugs to their HTML-encoded display names:
```
albir, alfaz-del-pi, altea, beniarda, benidorm, benifato, benimantell, bolulla,
callosa-den-sarria, calpe, confrides, finestrat, guadalest, la-nucia, orxeta,
polop, relleu, sella, tarbena, villajoyosa
```

### `patch_nonES_locality_seo(string $html, string $path, string $lang): string`
- Returns early for `$lang === 'es'` (ES already handled by `patch_es_p1_location_page`)
- Matches path against lang-specific regex patterns:
  - EN: `/en/air-conditioning-{slug}/`
  - DE: `/de/klimaanlage-{slug}/`
  - NL: `/nl/airco-{slug}/`
  - RU: `/ru/konditsioner-{slug}/`
  - NO: `/no/aircondition-{slug}/`
- Extracts `$slug`, looks up display name, injects localized title/meta/canonical
- Calls `patch_snapshot_seo_meta()` for actual replacement

**Title patterns:**
- EN: `Air conditioning in {CITY} | +QUECLIMA`
- DE: `Klimaanlage in {CITY} | +QUECLIMA`
- NL: `Airco in {CITY} | +QUECLIMA`
- RU: `Кондиционер в {CITY} | +QUECLIMA` (HTML entities in source)
- NO: `Aircondition i {CITY} | +QUECLIMA`

Call added in `patch_snapshot_html()` pipeline between
`patch_es_p1_location_page` and `patch_locality_hero_image`.

---

## Validation results

All **120/120** locality landings validated ✅

| Lang | Status |
|------|--------|
| ES   | 20/20 OK |
| EN   | 20/20 OK |
| DE   | 20/20 OK |
| NL   | 20/20 OK |
| RU   | 20/20 OK |
| NO   | 20/20 OK |

**Checks performed per URL:**
- Title contains lang-specific keyword (not generic home title)
- Canonical URL is `https://masqueclima.es/{lang}/{prefix}-{slug}/`
- hreflang count = 19
- x-default points to ES equivalent
- No PHP warnings or fatal errors

---

## Regression

- Home pages (all 6 langs): ✅ titles unchanged
- Legal pages (EN legal-notice, NL privacybeleid): ✅ titles unchanged
- `public/sitemap.xml`: no changes (0 diff lines)
- `public/robots.txt`: no changes (0 diff lines)

---

## Files changed

- `app/front_controller.php`: +71 lines
  - Added `locality_display_names()` function
  - Added `patch_nonES_locality_seo()` function
  - Added call in `patch_snapshot_html()` pipeline

## PHP lint

All PHP files lint clean: `No syntax errors detected`

## Git state

Branch: `fix/legacy-php-dev-stabilization`  
Only modified file: `app/front_controller.php` (71 insertions)  
No commit made (per project rules).
