# Checklist dev legacy

## HTTP

- [ ] `/` devuelve `301` a `/es/`.
- [ ] `/es/`, `/en/`, `/de/`, `/nl/`, `/ru/`, `/no/` devuelven `200`.
- [ ] Las 126 URLs canonicas del sitemap devuelven `200`.
- [ ] Reformas vivas devuelven `200` y no estan en sitemap.
- [ ] URL inexistente devuelve `404`.
- [ ] Ningun enlace visible del body apunta a `https://masqueclima.es`.
- [ ] Header y selector de idiomas usan rutas relativas del host actual.

## SEO dev

- [ ] `robots.txt` dev contiene `Disallow: /`.
- [ ] Header `X-Robots-Tag: noindex, nofollow, noarchive` presente.
- [ ] Sitemap dev conserva 126 URLs y no lista `/`.
- [ ] Canonical absoluto correcto.
- [ ] `x-default` del grupo home apunta a `/es/`.

## Funcional

- [ ] Popup abre.
- [ ] Formulario envia a `/contact-submit.php`.
- [ ] POST valido redirige con `sent=1` y genera log fallback.
- [ ] WhatsApp apunta a `https://wa.me/34613026600`.
- [ ] CSS, JS, hero, logo, flags y marcas cargan `200`.
- [ ] Hero renderiza video/imagen como fondo full-cover, sin caja de video desplazada.

## Seguridad

- [ ] `/.env` no accesible.
- [ ] `/app/`, `/views/`, `/vendor/`, `/storage/` no accesibles.
- [ ] `nginx -t` OK.
- [ ] `php -l` OK.
