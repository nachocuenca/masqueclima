# Notas de implementacion Fase P1

Fecha: 2026-05-27  
Rama: `fix/legacy-php-dev-stabilization`

## Alcance implementado

Se implemento una Fase P1 controlada en la web legacy PHP, sin redisenar, sin migrar a Next, sin borrar URLs vivas y sin tocar produccion/Nicalia.

Rutas nuevas servidas por `app/front_controller.php`:

- `/es/servicios/`
- `/es/zonas/`
- `/es/blog/`

Las rutas reutilizan la carcasa legacy del snapshot ES: header, footer, estilos, modal de presupuesto, formulario, WhatsApp flotante y scripts existentes.

## Navegacion principal

Se unifico el menu principal desde una funcion comun en `app/helpers.php`:

- `primary_nav_items()`
- `localized_hub_url()`

Menu ES:

- Inicio
- Metodo
- Nosotros
- Zona
- FAQ
- Contacto
- Selector de idioma

En idiomas sin hub real de servicios/zonas/guias no se inventan rutas extranjeras. El menu muestra home, Metodo, FAQ y contacto, manteniendo el selector de idioma.

Elementos retirados del header principal:

- Servicios
- Zonas
- Guias
- Ciudades
- Reformas integrales

Notas:

- Metodo, Quienes somos, Zona, FAQ y Contacto vuelven a ser las anclas principales del header.
- Servicios, Zonas y Guias quedan accesibles desde el footer ES y desde enlaces contextuales de la home.
- Ciudades queda consolidado en `/es/zonas/`, sin item propio en header.
- La ruta tecnica `/es/blog/` sigue viva, pero el concepto visible es `Guias`; no entra en sitemap mientras no tenga guias reales publicadas.
- Reformas sigue vivo, pero sale de la navegacion principal por foco de climatizacion.
- El selector de idioma usa URLs relativas al host actual; no salta a produccion.
- La home conserva sus secciones originales y el menu enlaza a `#metodo`, `#nosotros`, `#zona`, `#faq` y `#contacto`.
- Los hubs usan un hero compacto para que el contenido principal aparezca antes sin cambiar la estetica base.
- El footer ES enlaza solo a `/es/servicios/`, `/es/zonas/` y `/es/blog/`.

## Sitemap

No se modifico `public/sitemap.xml`.

Decision: no anadir las rutas nuevas al sitemap hasta validar en dev/despliegue que quedan listas, estables e indexables. El indice `/es/blog/` no contiene articulos publicados todavia, por lo que conviene esperar antes de incluirlo.

## Hreflang

No se inventaron equivalentes extranjeros para los hubs.

Los hubs ES no imprimen hreflang a EN/DE/NL/RU/NO porque esas paginas aun no existen. Las landings existentes conservan su estructura internacional actual.

## Landings P1 tocadas

El front controller aplica mejoras runtime solo para estas URLs ES:

- `/es/aire-acondicionado-benidorm/`
- `/es/aire-acondicionado-altea/`
- `/es/aire-acondicionado-calpe/`
- `/es/aire-acondicionado-finestrat/`
- `/es/aire-acondicionado-la-nucia/`

Cambios:

- Title unico por ciudad.
- Meta description unica por ciudad.
- Canonical preservado.
- H1 preservado.
- Bloque de enlazado interno hacia `/es/servicios/`, `/es/zonas/` y ciudades cercanas.
- CTA al popup de presupuesto preservando `#quoteModal`.

No se tocaron snapshots ni idiomas extranjeros para estas mejoras.

## Rutas no implementadas

No se crearon guias ni articulos reales.

No se crearon landings individuales de servicio como:

- `/es/servicios/instalacion-aire-acondicionado/`
- `/es/servicios/mantenimiento-climatizacion/`
- `/es/servicios/reparacion-aire-acondicionado/`

Quedan para fase posterior.

## Reformas

Reformas no se elimino, no se redirigio, no se noindexo y no se anadio al sitemap.

Se retiro de la navegacion principal y se mantiene como decision pendiente segun `docs/REFORMAS_DECISION_PENDING.md`.

## Validaciones locales ejecutadas

- `php -l app/front_controller.php`
- `php -l` recursivo en `app`, `views`, `public` y `public_html`
- Servidor local PHP sobre `public/index.php`
- Validacion local de 200 para:
  - `/es/servicios/`
  - `/es/zonas/`
  - `/es/blog/`
  - `/es/aire-acondicionado-benidorm/`
  - `/es/aire-acondicionado-altea/`
  - `/es/aire-acondicionado-calpe/`
  - `/es/aire-acondicionado-finestrat/`
  - `/es/aire-acondicionado-la-nucia/`
- Validacion local de homes:
  - `/` responde 301 a `/es/`
  - `/es/`, `/en/`, `/de/`, `/nl/`, `/ru/`, `/no/` responden 200
- Validacion de que los hubs incluyen `quoteModal`, `contact-submit.php` y `btn-whatsapp-pulse`
- Validacion de que los hubs no generan hreflang a idiomas sin equivalente real
- Validacion de que el cuerpo HTML no contiene enlaces visibles `href="https://masqueclima.es..."`
- Validacion de header principal en home, hubs, landing P1, reformas, EN y NO
- Validacion de footer ES con Servicios, Zonas y Guias, sin Contacto ni WhatsApp
- Validacion de selector de idioma con URLs relativas (`/en/`, `/no/`, etc.)
- Validacion de copy visible sin terminos internos como `landing`, `hub`, `P1`, `URL`, `placeholder`, `sitemap` o `contenido fino`
- `bash -lc 'find ... | xargs ... php -l'` no pudo ejecutarse porque no hay `/bin/bash` disponible en este entorno; se ejecuto validacion PowerShell equivalente.

## Pendiente de validacion tras despliegue a dev

Ejecutar contra `https://dev.masqueclima.es`:

- `curl -I https://dev.masqueclima.es/es/servicios/`
- `curl -I https://dev.masqueclima.es/es/zonas/`
- `curl -I https://dev.masqueclima.es/es/blog/`
- Validar `X-Robots-Tag: noindex, nofollow, noarchive`
- Validar que `/robots.txt` sigue con `Disallow: /`
- Validar que `/`, `/es/`, `/en/`, `/de/`, `/nl/`, `/ru/`, `/no/` siguen OK

Comprobacion realizada antes de desplegar estos cambios: dev sigue sirviendo la version anterior, por lo que `/es/servicios/`, `/es/zonas/` y `/es/blog/` aun responden 404 en `https://dev.masqueclima.es`. Las homes siguen 200 y dev mantiene `X-Robots-Tag: noindex, nofollow, noarchive`.

## Riesgos pendientes

- Los hubs aun no estan en sitemap; decision pendiente tras validar despliegue.
- `/es/blog/` es un indice de Guias, pero las guias reales siguen pendientes.
- Las landings P1 mejoran metadatos/enlaces, pero el cuerpo base sigue siendo parecido entre ciudades.
- Reformas sigue vivo y fuera del sitemap; requiere decision comercial/SEO posterior.

## Pulido UX y conversion

- Los hubs `/es/servicios/`, `/es/zonas/` y `/es/blog/` usan hero compacto y centrado.
- Los botones azules mantienen hover azul; el hover naranja queda eliminado.
- Las tarjetas de servicios se maquetan como columnas flex para alinear el CTA al fondo.
- `/es/zonas/` incorpora un mapa SVG ligero de Marina Baixa como apoyo visual.
- Los hubs cierran con una caja final reutilizable de `Solicita tu presupuesto` antes del footer.
- El boton contextual de Guias en FAQ usa estilo claro para integrarse mejor sobre el bloque oscuro.

## Refactor tecnico de partials legacy

- Se extrajeron bloques visuales grandes de `app/front_controller.php` a partials reutilizables en `views/partials/`.
- La CTA final, el hero compacto, los estilos de hubs, las tarjetas de servicios, el visual de zonas y las tarjetas de Guias se renderizan ahora con partials.
- Se anadio `render_partial()` en `app/helpers.php` con restriccion de nombre y comprobacion de ruta dentro de `views/partials/`.
- No se modificaron rutas, sitemap, copy visible ni comportamiento de popup/WhatsApp.
- Detalle tecnico en `docs/LEGACY_PARTIALS_REFACTOR_NOTES.md`.

## Siguiente fase recomendada

No implementado todavia. Cuando se apruebe la siguiente fase, crear paginas SEO especificas de servicio:

- `/es/servicios/instalacion-aire-acondicionado/`
- `/es/servicios/mantenimiento-climatizacion/`
- `/es/servicios/reparacion-aire-acondicionado/`
- `/es/servicios/aerotermia-bomba-calor/`
- `/es/servicios/energia-solar-termica/`
