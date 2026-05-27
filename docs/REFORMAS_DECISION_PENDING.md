# Reformas: decision pendiente

Fecha: 2026-05-27  
Alcance: auditoria y opciones. No aplicar cambios todavia.

## Estado actual

Reformas existe como grupo vivo de URLs en 6 idiomas y responde 200 en dev.

| Idioma | URL | Estado dev | Clasificacion |
| --- | --- | ---: | --- |
| ES | `/es/reformas-integrales-benidorm/` | 200 | Reformas/servicio dudoso |
| EN | `/en/benidorm-renovations/` | 200 | Reformas/servicio dudoso |
| DE | `/de/renovierungen-benidorm/` | 200 | Reformas/servicio dudoso |
| NL | `/nl/verbouwingen-benidorm/` | 200 | Reformas/servicio dudoso |
| RU | `/ru/remont-benidorm/` | 200 | Reformas/servicio dudoso |
| NO | `/no/oppussing-benidorm/` | 200 | Reformas/servicio dudoso |

## Donde aparece

- Existe en `app/snapshots/` como 6 snapshots.
- Estaba enlazado desde la navegacion original de los snapshots.
- Tras el ajuste de navegacion P1, el menu comun ya no muestra Reformas.
- No aparece en `public/sitemap.xml`.
- No aparece en el sitemap servido por dev.

## SEO tecnico observado

| Elemento | Estado |
| --- | --- |
| Status | 200 en dev |
| Canonical | Canonical propio por idioma |
| Hreflang | Grupo internacional con x-default a ES |
| Title | Generico de climatizacion, no especifico de reformas |
| H1 | Especifico de reformas |
| Sitemap | No incluido |
| Enlazado interno | Fuera del header y del footer principal tras ajuste P1 |
| JSON-LD | Contiene datos de negocio/local business |

Riesgo adicional: los breadcrumbs JSON-LD de reformas no parecen alineados con las URLs reales en todos los idiomas. Ejemplos detectados: EN/DE/NL/RU/NO apuntan a rutas tipo `/{lang}/reformas-integrales-benidorm/` en el breadcrumb, aunque el canonical usa el slug traducido. En ES el breadcrumb JSON-LD apunta a `https://masqueclima.es/reformas-integrales-benidorm/` sin prefijo `/es/`. No corregir todavia; queda anotado para fase posterior.

## Decision recomendada ahora

Mantener reformas vivo y dejar la decision para una fase posterior.

No aplicar ahora:

- No borrar.
- No redirigir.
- No noindexar todavia.
- No anadir al sitemap todavia.
- No volver a meter en header ni footer principal sin una decision comercial/SEO.

## Estado tras ajuste de navegacion P1

Reformas se elimina de la navegacion principal y del footer principal porque esta web queda centrada en climatizacion.

La URL sigue viva temporalmente para no romper enlaces y poder auditarla despues:

- `/es/reformas-integrales-benidorm/`
- `/en/benidorm-renovations/`
- `/de/renovierungen-benidorm/`
- `/nl/verbouwingen-benidorm/`
- `/ru/remont-benidorm/`
- `/no/oppussing-benidorm/`

Decision mantenida:

- No borrar.
- No redirigir.
- No anadir al sitemap.
- No convertir en blog.
- Mantener fuera de header y footer principal.
- Reabrir decision cuando exista proyecto/web especifico de reformas.

## Opciones para fase posterior

| Opcion | Cuando elegirla | Accion |
| --- | --- | --- |
| Mantener | Si reformas genera leads o es servicio real a impulsar | Crear arquitectura propia o encajar como servicio, mejorar title/description/contenido, corregir JSON-LD |
| Noindex | Si se quiere mantener para usuarios pero no competir SEO | Anadir `noindex,follow`, mantener enlaces necesarios, quitar del sitemap si estuviera |
| Redirigir | Si ya no se ofrece reformas o no interesa posicionarla | 301 a destino relevante con mapa aprobado; preservar idioma |
| Dejar para despues | Si faltan datos de rendimiento | Mantener 200, no sitemap, documentar riesgos |

## Recomendacion si se mantiene

Si la empresa quiere vender reformas:

- Tratarlo como servicio separado, no como blog.
- Mantener URLs actuales vivas.
- Mejorar title y meta description por idioma.
- Crear contenido mas claro sobre alcance, zonas, plazos, garantias y casos.
- Corregir breadcrumbs JSON-LD.
- Decidir si debe entrar en sitemap despues de mejorar contenido.
- Revisar si debe tener arquitectura propia fuera de esta web o un proyecto especifico.

## Recomendacion si no se mantiene

Si reformas no es servicio activo:

- No borrar directamente.
- Revisar datos de Search Console, Analytics y leads.
- Preparar mapa 301 por idioma.
- Elegir destino equivalente o la home de idioma solo como ultimo recurso.
- Mantener `/no/` y sus equivalentes hasta ejecutar la decision.

## Riesgos actuales

- Topical dilution: reformas puede alejar la web de climatizacion si se promociona demasiado sin estrategia.
- URLs vivas no promocionadas desde header/footer mientras no haya decision.
- Title generico de climatizacion en una pagina de reformas.
- Breadcrumb JSON-LD con URLs inconsistentes.
- Decision pendiente puede bloquear una arquitectura limpia de servicios.
