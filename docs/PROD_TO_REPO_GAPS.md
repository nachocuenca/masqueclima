# Gaps produccion vs repo

## Gaps detectados al partir de `origin/main`

- El repo no representaba completamente la web viva: faltaban snapshots o plantillas para las `126` URLs canonicas.
- `/no/` no estaba completo en configuracion/traducciones/flag local.
- No habia cobertura repo para las seis URLs vivas de reformas.
- El sitemap local no debia listar `/` como canonica.
- `lang_url('es')` tenia que dejar de resolver a `/` para evitar el choque con el 403.
- Faltaba endpoint legacy restaurado para `/contact-submit.php`.
- Faltaban assets criticos de produccion: hero, favicon, flag noruega y algunos hero de landings.
- La rama de trabajo anterior contenia WIP Next/rediseno y fue aislada en stash antes de crear esta rama legacy.

## Alineacion aplicada

- Se capturaron snapshots HTML de produccion para `126` URLs canonicas y `6` reformas vivas.
- Se anadio front controller legacy que sirve snapshots y aplica parches tecnicos minimos.
- Se restauro soporte `no` sin eliminar ni redirigir.
- Se regenero `public/sitemap.xml` con `126` URLs, sin `/`.
- Se restauro `/contact-submit.php` con validacion, honeypot, CSRF y log fallback.
- Se preparo Nginx/PHP-FPM para `dev.masqueclima.es` con noindex.
