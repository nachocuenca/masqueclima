# Fix hero dev

Fecha: 2026-05-27
Entorno: `https://dev.masqueclima.es`

## Incidencia

El hero en dev mostraba imagen y video mal apilados: el video aparecia como una caja desplazada hacia la esquina superior izquierda y no como fondo full-cover.

## Causa

Dev estaba sirviendo una version antigua de `public/assets/css/styles.css` de `9222` bytes. Produccion sirve una version actual de `16294` bytes con reglas especificas para:

- `.hero`
- `.hero-bg`
- `.hero-img`
- `.hero-video`
- overlay con `hero::before`
- `.hero-video.is-ready`
- ocultacion del video en movil

La version antigua usaba z-index negativos y no tenia las reglas completas para `.hero-img,.hero-video` en `inset:0;width:100%;height:100%;object-fit:cover`.

## Correccion

Se restauro `public/assets/css/styles.css` desde produccion viva.

Reglas clave ahora presentes:

- `.hero { position: relative; overflow: hidden; min-height: 100vh; z-index: 0; }`
- `.hero-bg { position: absolute; inset: 0; z-index: 0; overflow: hidden; }`
- `.hero-img,.hero-video { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; z-index: 0; }`
- `.hero::before { z-index: 1; pointer-events: none; }`
- `.hero .container { position: relative; z-index: 2; }`
- `.hero-video { opacity: 0; transition: opacity .35s ease; }`
- `.hero-video.is-ready { opacity: 1; }`

## Assets

- `/assets/img/hero.mp4` debe devolver `200`.
- `/assets/img/hero1.jpg` debe devolver `200`.
- `/assets/img/hero1.webp` debe devolver `200`.
- `/assets/css/styles.css` debe devolver `200` y tener las reglas anteriores.

## Decision

No se redisenio el hero. Se restauro el CSS que produccion ya usa para que dev vuelva a renderizar igual o casi igual.
