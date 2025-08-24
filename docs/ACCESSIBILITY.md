# Accesibilidad

## Principios
- Contraste AA mínimo (4.5:1) para texto normal.
- Mantener focus visible en enlaces y botones.
- Asegurar navegación completa con teclado.

## Roles y ARIA
- Navbar: `<nav aria-label="main">` con lista `<ul>`.
- Carrusel Swiper: añadir `aria-roledescription="carousel"` y botones con `aria-label`.
- Formularios: cada `<input>` con `label` asociado y `aria-required` cuando proceda.

## Imágenes
- `alt` descriptivo en todas las imágenes.
- Para íconos decorativos usar `alt=""` o `role="presentation"`.

## Focus management
- En modales o sliders, enfocar el primer elemento interactivo.
- Evitar que el focus quede atrapado en elementos ocultos.
