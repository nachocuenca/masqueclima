# Guía de internacionalización (i18n)

## Claves y traducciones
- Traducciones en `app/lang/{lang}.php` devuelven arrays asociativos.
- Las claves se nombran en minúsculas con puntos (`form.ok`).
- Si una clave falta en un idioma, se muestra el texto por defecto en español.

## Añadir un nuevo idioma
1. Crear `app/lang/{nuevo}.php` copiando `es.php`.
2. Añadir el código de idioma a `config/brand.php` (`langs` y, si aplica, `default_lang`).
3. Añadir bandera `public/assets/img/flags/{nuevo}.svg`.
4. Actualizar `print_og_locales()` en `helpers.php` para mapear locale.

## Normas
- No incluir dirección física en ninguna traducción.
- Evitar hardcodear textos en vistas; usar `t('clave')`.
- Para accesibilidad usar `aria-label` traducido cuando la bandera sea un link (`<img alt>` + `<span class="visually-hidden">`).

## Banderas
- Se obtienen con `flag_url($lang)`.
- Para enlaces de cambio de idioma usar `lang_switch_url($lang)`.
