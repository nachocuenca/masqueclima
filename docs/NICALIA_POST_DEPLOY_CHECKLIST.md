# Checklist post deploy Nicalia

- [ ] `/` devuelve `301` a `/es/`.
- [ ] `/es/`, `/en/`, `/de/`, `/nl/`, `/ru/`, `/no/` devuelven `200`.
- [ ] Sitemap produccion tiene 126 URLs, no lista `/`.
- [ ] Robots produccion permite crawling y apunta a `https://masqueclima.es/sitemap.xml`.
- [ ] Canonical absoluto correcto.
- [ ] Hreflang reciproco correcto.
- [ ] `x-default` home apunta a `https://masqueclima.es/es/`.
- [ ] Popup abre.
- [ ] `/contact-submit.php` acepta POST valido.
- [ ] WhatsApp funciona.
- [ ] CSS/JS/hero/logo/flags/marcas `200`.
- [ ] Reformas vivas siguen `200`.
- [ ] No hay 403/500 en rutas criticas.
- [ ] Cache LiteSpeed limpiada.
