# Monitorización post deploy

Primeras 24 horas:

- Comprobar `/`, `/es/`, `/en/`, `/de/`, `/nl/`, `/ru/`.
- Comprobar robots y sitemap.
- Ejecutar `npm run seo:audit -- https://masqueclima.es`.
- Revisar logs Nginx de 404/500.
- Revisar Search Console: cobertura, sitemaps, canónicas.
- Revisar que no hay noindex accidental.

Primera semana:

- Comparar tráfico orgánico por URL.
- Revisar queries de marca y localidad.
- Revisar errores de rastreo.
- Revisar URLs antiguas con hits y añadir 301 si faltan.
- Revisar Core Web Vitals.

Alertas manuales:

- `/` no puede devolver 403.
- `sitemap.xml` no puede listar dev.
- `robots.txt` producción no puede bloquear `/`.
- `dev.masqueclima.es` no puede indexarse.
