# Riesgos SEO

## P0

- `https://masqueclima.es/` devuelve `403`. Debe convertirse en redirección permanente a `/es/` en el cutover.
- `x-default` vivo apunta a `/`; mientras `/` sea 403, es una señal internacional defectuosa.
- Producción viva tiene muchas más URLs que el repo local. Riesgo de pérdida de tráfico si no se importan o redirigen.
- Producción viva tiene textos/H1 distintos en homes extranjeras respecto al repo local exportado. `compare-prod-vs-dev` detectó diferencias en `en/de/nl/ru`; antes de producción hay que decidir si se importa el contenido vivo o se acepta el contenido del repo.

## P1

- El idioma `no` aparece en producción viva pero no en el alcance definido. Hay que decidir conservar, redirigir o retirar.
- Páginas nuevas españolas no tienen equivalentes reales en otros idiomas. Correcto: no se emiten hreflang para ellas.
- Las reseñas Google no deben inventarse ni copiarse manualmente. Pendiente integración oficial Business Profile API.
- Elfsight queda como fallback temporal, no como arquitectura final de reseñas.
- Footer legacy tenía claves inconsistentes.

## P2

- Google Maps iframe sigue siendo externo.
- Las fotos/reseñas de Google requieren OAuth y política de cache.
- `npm audit` reporta 2 vulnerabilidades moderadas en dependencias; revisar antes de producción sin aplicar `--force` a ciegas.

## Mitigaciones ya aplicadas

- Middleware redirige `/` a `/es/`.
- Staging devuelve `X-Robots-Tag: noindex, nofollow, noarchive`.
- `robots.txt` staging bloquea `/`.
- Sitemap staging queda vacío.
- Sitemap producción se genera dinámicamente desde rutas canónicas reales.
- Metadata API emite canonical absoluto.
- Homes emiten `hreflang` entre `es/en/de/nl/ru` y `x-default` a `/es/`.
