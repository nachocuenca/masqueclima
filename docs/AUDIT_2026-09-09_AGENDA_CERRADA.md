# Auditoría operativa — cierre temporal de agenda

Fecha: 2026-09-09  
Base auditada: `main` @ `810d91af1676cad93afb084316dfcaf93a8237c7`  
Objetivo: detener temporalmente la captación de nuevos trabajos sin perjudicar el activo SEO de +QUECLIMA.

## Arquitectura real observada

La arquitectura actual de `main` ha evolucionado respecto a documentación antigua del proyecto:

- `public_html/index.php` carga `masqueclima/public/index.php`.
- `public/index.php` carga `app/front_controller.php`.
- Las homes y landings locales se sirven principalmente desde snapshots HTML de `app/snapshots/`.
- `app/front_controller.php` aplica `patch_snapshot_html()` al HTML antes de entregarlo.
- Hubs y páginas de servicio se renderizan dinámicamente y también pasan por el pipeline de patching.
- Idiomas activos: `es`, `en`, `de`, `nl`, `ru`, `no`.
- `views/home.php` sigue en el repositorio, pero no es la fuente directa de la home pública `/es/` en el flujo actual.

Conclusión: un estado comercial global debe aplicarse al HTML final, no editando únicamente `views/home.php`.

## Superficie de captación detectada

La web contiene varios puntos de entrada comercial:

- CTA de presupuesto en hero.
- Modal `#quoteModal`.
- Formularios de contacto/presupuesto.
- CTA final `views/partials/final_budget_cta.php`.
- CTA tras reseñas.
- Teléfono y WhatsApp.

Por ello, un simple banner no evita por sí solo que sigan llegando solicitudes.

## Criterio SEO

Durante el cierre temporal NO se modifican:

- URLs.
- Canonical.
- Hreflang.
- Sitemap.
- Robots.
- Titles/descriptions SEO.
- Contenido local o de servicios.
- JSON-LD.

El cierre es comercial, no técnico. La web permanece accesible e indexable.

## Implementación preparada

Se añade un estado global reversible:

- `app.accepting_new_work = false` por defecto.
- Se puede reabrir sin modificar código definiendo `ACCEPTING_NEW_WORK=1`.
- `app/site_status.php` transforma únicamente respuestas HTML.
- Inserta un aviso visible y localizado de `AGENDA TEMPORALMENTE CERRADA`.
- Mantiene teléfono y WhatsApp identificados para trabajos ya en curso.
- Oculta los disparadores del modal de presupuesto, el modal, formularios de captación, el CTA final de presupuesto y el CTA de reseñas.
- No altera healthchecks ni respuestas no HTML.

## Avisos de calor

En la revisión de `main` no se ha localizado un componente o texto específico de “aviso de calor”, “ola de calor”, “altas temperaturas” o integración meteorológica. No debe eliminarse código a ciegas. Si el aviso continúa visible en producción después del despliegue, habrá que identificar su origen exacto (versión desplegada, caché, widget o código fuera del HEAD auditado) antes de retirarlo.

## Validación requerida antes de producción

1. PHP lint de `app`, `views`, `public` y `public_html`.
2. Renderizar al menos `/es/`, `/en/`, una landing local y una página de servicio.
3. Confirmar un único banner visible y correctamente localizado.
4. Confirmar que no se abre `#quoteModal` ni aparecen formularios de nueva captación.
5. Confirmar que teléfono/WhatsApp siguen disponibles para clientes con trabajos en curso.
6. Confirmar canonical/hreflang/JSON-LD sin cambios.
7. Ejecutar `tools/deploy-verify.sh` y comprobar `?__health=1` tras el despliegue.
8. Vaciar caché de Nicalia/LiteSpeed y navegador.
