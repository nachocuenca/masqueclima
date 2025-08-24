# Guía SEO

## Canónicas y `hreflang`
- `layout.php` imprime `<link rel="canonical">` y `hreflang` absolutos mediante `lang_url()`.
- `x-default` apunta a `/es/`.

## Metadatos sociales
- Etiquetas OG/Twitter generadas en `layout.php`.
- `print_og_locales()` define `og:locale` y alternates.

## JSON‑LD
Se generan automáticamente en `helpers.php`:
- `Organization` sin dirección física.
- `HVACBusiness` con datos de contacto y servicios.
- `WebSite` con `SearchAction`.
- `BreadcrumbList` mínimo con Home.
- `FAQPage` solo en la home cuando existen traducciones.

## Checklist
- [ ] Canonical absoluta y sin parámetros.
- [ ] `hreflang` para todos los idiomas + `x-default`.
- [ ] `og:image` accesible (`200 OK`).
- [ ] Validar JSON‑LD en [Rich Results Test](https://search.google.com/test/rich-results).
- [ ] `robots.txt` y `sitemap.xml` accesibles.
