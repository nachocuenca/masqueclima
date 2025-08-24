# Arquitectura de la aplicación

```
public_html/.htaccess  →  mascara front-controller
           ↓
masqueclima/public/index.php  →  router simple
           ↓
        views/*.php  ← helpers.php + config
```

## Componentes
- **Front controller**: `public/index.php` recibe todas las peticiones no estáticas.
- **Router**: analiza `REQUEST_URI`, detecta idioma (`es|en|de|nl|ru`) y determina la vista (`home` o `404`).
- **Vistas**: en `views/` con plantilla base `layout.php` que incluye `partials/header.php` y `partials/footer.php`.
- **Helpers**: `app/helpers.php` provee utilidades de URLs (`asset`, `lang_url`, `base_url`…), SEO y JSON‑LD.
- **Assets**: servidos desde `public/assets`, `public/img` y `public/video`.
- **i18n**: traducciones en `app/lang/{lang}.php`; la clave por defecto es `es`.

## Flujo de petición
1. Apache redirige todas las URL a `masqueclima/public/index.php` salvo archivos reales.
2. `index.php` carga configuración y helpers, resuelve idioma y vista.
3. Se renderiza `views/layout.php` que inserta meta-tags, JSON‑LD y recursos externos.
4. Los assets se referencian mediante `asset()` que aplica `rawurlencode` por segmento, evitando problemas con espacios o acentos.

## Dependencias externas
- **CDN**: Bootstrap, Swiper, Google Fonts.
- **JS embebido**: Elfsight para widgets y Swiper para carruseles.
- **PHPMailer**: usado para email de contacto (no incluido en fragmento).
