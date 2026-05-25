# Checklist de cutover SEO

No cortar a producción hasta completar todo.

- [ ] `npm run lint` pasa.
- [ ] `npm run build` pasa.
- [ ] `npm run seo:audit -- https://dev.masqueclima.es` pasa para staging.
- [ ] `dev.masqueclima.es/robots.txt` bloquea `/`.
- [ ] `dev.masqueclima.es` tiene `X-Robots-Tag: noindex, nofollow, noarchive`.
- [ ] Exportado sitemap vivo completo de `https://masqueclima.es/sitemap.xml`.
- [ ] Cada URL viva tiene mantener/redirigir/retirar documentado.
- [ ] Solucionado P0 `/` 403 con redirección a `/es/`.
- [ ] `x-default` apunta a `/es/`.
- [ ] Sitemap producción no incluye `/`, no incluye dev y no incluye URLs sin contenido.
- [ ] No hay `hreflang` falso en páginas españolas nuevas.
- [ ] Revisadas traducciones de homes.
- [ ] Reseñas Google oficiales integradas o decisión temporal aprobada (`docs/GOOGLE_REVIEWS_INTEGRATION.md`).
- [ ] Search Console preparado para monitorizar.
- [ ] Rollback probado en dev.
