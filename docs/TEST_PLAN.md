# Plan de pruebas

## Pruebas manuales
1. Navegar a `/`, `/en/`, `/de/`, `/nl/`, `/ru/` y verificar contenido correcto.
2. Enviar formulario de contacto con datos válidos e inválidos.
3. Cambiar de idioma usando las banderas.
4. Comprobar reproducción del vídeo hero y funcionamiento del carrusel Swiper.
5. Validar carga de Elfsight.

## Peticiones HTTP esperadas (200)
- `/` (`text/html`)
- `/en/` (`text/html`)
- `/assets/css/styles.css` (`text/css`)
- `/img/masqueclimalogo_.png` (`image/png`)
- `/video/0_Air_Conditioner_Remote_Control_3840x2160.mp4` (`video/mp4`)
- `/robots.txt` (`text/plain`)
- `/sitemap.xml` (`application/xml`)
- `/?__health=1` (`text/plain` `OK`)

## Lighthouse
- Ejecutar Lighthouse (modo móvil) en cada idioma.
- Comprobar métricas de CWV (LCP, FID, CLS).
