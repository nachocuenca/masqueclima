# Legacy Partials Refactor Notes

Fecha: 2026-05-27

## Objetivo

Reducir el peso de `app/front_controller.php` sin cambiar el resultado visible, el routing, el sitemap ni las URLs existentes.

## Alcance aplicado

Se extrajeron bloques visuales grandes y estables a partials:

- `views/partials/final_budget_cta.php`
- `views/partials/hub_hero.php`
- `views/partials/hub_styles.php`
- `views/partials/service_cards.php`
- `views/partials/zone_visual.php`
- `views/partials/guide_cards.php`

Tambien se anadio `render_partial()` en `app/helpers.php`.

## Criterio de seguridad

- Los partials solo se cargan por nombre simple con letras, numeros, guion y guion bajo.
- La ruta final debe resolverse dentro de `views/partials/`.
- Si el partial no existe o el nombre no es valido, devuelve una cadena vacia.
- Las funciones publicas internas existentes (`final_budget_cta_html()`, `hub_intro()`, `hub_styles()`) se mantienen como fachada para no tocar el flujo del controlador.

## Sin cambios funcionales intencionados

- No se cambio copy visible.
- No se cambiaron URLs.
- No se tocaron sitemap ni robots.
- No se tocaron popup, formulario ni WhatsApp.
- No se anadieron paginas SEO nuevas.
- No se modifico la navegacion de Reformas.

## Pendiente futuro

Antes de crear paginas SEO especificas de servicios, se podria extraer el render de cada hub completo a vistas dedicadas si el controlador vuelve a crecer.
