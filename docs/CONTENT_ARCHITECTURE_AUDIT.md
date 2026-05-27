# Auditoria de arquitectura SEO

Fecha: 2026-05-27  
Rama auditada: `fix/legacy-php-dev-stabilization`  
Alcance: documentacion y plan. No se han creado paginas nuevas, no se ha tocado produccion/Nicalia y no se ha hecho commit.

## Resumen ejecutivo

- La web legacy en dev sirve snapshots HTML desde `app/snapshots/`.
- El sitemap de repo (`public/sitemap.xml`) y el sitemap de dev (`https://dev.masqueclima.es/sitemap.xml`) contienen 126 URLs: 6 homes de idioma y 120 landings locales de aire acondicionado.
- No existen en dev ni en sitemap las rutas `/es/zonas/`, `/es/servicios/` ni `/es/blog/`; las tres responden 404 en dev.
- Reformas existe como snapshot vivo en 6 idiomas y responde 200 en dev, pero no esta en el sitemap.
- Las landings de ciudad no deben convertirse en blog: son paginas locales/transaccionales y ya tienen canonicals, hreflang, CTA, formulario y WhatsApp.
- El principal riesgo actual no es tecnico, sino de contenido: titles y descriptions duplicadas en todas las ciudades ES, cuerpo base muy repetido y algunas ciudades pequenas con riesgo alto de thin/near-duplicate content.

## Inventario del sitemap

### Homes por idioma

| Tipo | URL |
| --- | --- |
| Home ES | `/es/` |
| Home EN | `/en/` |
| Home DE | `/de/` |
| Home NL | `/nl/` |
| Home RU | `/ru/` |
| Home NO | `/no/` |

Todas responden 200 en dev. `/no/` esta vivo y debe preservarse.

### Landings de zona

Hay 20 grupos de zona en 6 idiomas. En ES las URLs vivas son:

| Zona | URL ES |
| --- | --- |
| Albir | `/es/aire-acondicionado-albir/` |
| Alfaz del Pi | `/es/aire-acondicionado-alfaz-del-pi/` |
| Altea | `/es/aire-acondicionado-altea/` |
| Beniarda | `/es/aire-acondicionado-beniarda/` |
| Benidorm | `/es/aire-acondicionado-benidorm/` |
| Benifato | `/es/aire-acondicionado-benifato/` |
| Benimantell | `/es/aire-acondicionado-benimantell/` |
| Bolulla | `/es/aire-acondicionado-bolulla/` |
| Callosa den Sarria | `/es/aire-acondicionado-callosa-den-sarria/` |
| Calpe | `/es/aire-acondicionado-calpe/` |
| Confrides | `/es/aire-acondicionado-confrides/` |
| Finestrat | `/es/aire-acondicionado-finestrat/` |
| Guadalest | `/es/aire-acondicionado-guadalest/` |
| La Nucia | `/es/aire-acondicionado-la-nucia/` |
| Orxeta | `/es/aire-acondicionado-orxeta/` |
| Polop | `/es/aire-acondicionado-polop/` |
| Relleu | `/es/aire-acondicionado-relleu/` |
| Sella | `/es/aire-acondicionado-sella/` |
| Tarbena | `/es/aire-acondicionado-tarbena/` |
| Villajoyosa | `/es/aire-acondicionado-villajoyosa/` |

Patrones por idioma:

| Idioma | Patron |
| --- | --- |
| ES | `/es/aire-acondicionado-{zona}/` |
| EN | `/en/air-conditioning-{zona}/` |
| DE | `/de/klimaanlage-{zona}/` |
| NL | `/nl/airco-{zona}/` |
| RU | `/ru/konditsioner-{zona}/` |
| NO | `/no/aircondition-{zona}/` |

### Landings de servicio

No hay landings de servicio vivas en dev ni en sitemap.

Existen contenidos de servicio en repo (`app/content/services/es.php`) y vistas (`views/services.php`, `views/service.php`), pero el front controller actual sirve snapshots y no expone esas rutas. Ejemplo verificado: `/es/instalacion-aire-acondicionado/` responde 404 en dev.

Servicios candidatos detectados en repo:

| Servicio candidato | Estado actual |
| --- | --- |
| Instalacion de aire acondicionado | Repo-only, no vivo |
| Mantenimiento de climatizacion | Repo-only, no vivo |
| Reparacion de averias | Repo-only, no vivo |
| Calefaccion por bomba de calor y aerotermia | Repo-only, no vivo |
| Instalaciones electricas y boletines | Repo-only, no vivo |
| Fontaneria | Repo-only, no vivo |

### Reformas

Reformas existe y responde 200 en dev, pero no esta en el sitemap.

| Idioma | URL |
| --- | --- |
| ES | `/es/reformas-integrales-benidorm/` |
| EN | `/en/benidorm-renovations/` |
| DE | `/de/renovierungen-benidorm/` |
| NL | `/nl/verbouwingen-benidorm/` |
| RU | `/ru/remont-benidorm/` |
| NO | `/no/oppussing-benidorm/` |

Tambien esta enlazado desde la navegacion de los snapshots. La decision debe quedar pendiente: no borrar, no redirigir y no noindexar todavia sin datos.

### Directorios detectados

| Ruta | Dev | Sitemap | Snapshot |
| --- | ---: | ---: | ---: |
| `/es/zonas/` | 404 | No | No |
| `/es/servicios/` | 404 | No | No |
| `/es/blog/` | 404 | No | No |

## Clasificacion de URLs

| Tipo | Ejemplos | Clasificacion | Decision |
| --- | --- | --- | --- |
| Home idioma | `/es/`, `/en/`, `/no/` | Home idioma | Mantener |
| Ciudad/zona | `/es/aire-acondicionado-benidorm/` | Landing local/transaccional | Mantener y mejorar |
| Servicio | Futuro `/es/servicios/instalacion-aire-acondicionado/` | Landing servicio | Crear en fase posterior |
| Blog/guia | Futuro `/es/blog/.../` | Informacional | Crear separado de zonas |
| Reformas | `/es/reformas-integrales-benidorm/` | Reformas/servicio dudoso | Mantener vivo y auditar |

## Auditoria de landings de ciudad

Se revisaron las 20 URLs ES de ciudad en snapshots y se contrasto una muestra en dev.

| Elemento | Estado actual | Riesgo |
| --- | --- | --- |
| H1 | Unico por ciudad, formato `Aire acondicionado en {Ciudad}` | Bajo |
| Title | Duplicado en todas las ciudades ES: title generico de home | Alto |
| Description | Duplicada en todas las ciudades ES: description generica de home | Alto |
| Canonical | Unico y correcto por URL de ciudad | Bajo |
| Texto local | 617-660 palabras aprox.; base muy repetida con ciudad sustituida | Medio/alto |
| CTA | Presupuesto, telefono, WhatsApp y formulario presentes | Bajo |
| FAQ | FAQ visible y JSON-LD presente | Bajo/medio |
| Enlaces internos | Cada ciudad enlaza al listado de 20 URLs ES de ciudad y a reformas | Medio |
| Hreflang | 7 alternates: 6 idiomas + x-default | Bajo |
| Idioma noruego | `/no/` y 20 landings NO existen con canonical propio | Bajo |
| Thin content | Mas riesgo en pueblos pequenos con FAQ generica | Alto en P3 |

Conclusion: las paginas de ciudad son validas como landings locales, pero no son suficientemente unicas para escalar sin una fase de mejora de contenido. La prioridad debe ser enriquecer las ciudades con demanda comercial antes de crear muchas URLs nuevas.

## Arquitectura final recomendada

La arquitectura debe crecer por capas, preservando las URLs actuales:

```text
/es/
/es/servicios/
/es/servicios/instalacion-aire-acondicionado/
/es/servicios/mantenimiento-climatizacion/
/es/servicios/reparacion-aire-acondicionado/
/es/servicios/bomba-de-calor-aerotermia/
/es/zonas/
/es/aire-acondicionado-benidorm/
/es/aire-acondicionado-altea/
/es/aire-acondicionado-calpe/
/es/aire-acondicionado-finestrat/
/es/aire-acondicionado-la-nucia/
/es/blog/
/es/blog/{articulo-informacional}/
```

Notas:

- `/es/zonas/` debe ser un hub/listado, no el nuevo padre obligatorio de las ciudades.
- Las URLs actuales de ciudad deben preservarse tal cual para evitar perdida de senales.
- `/es/blog/` debe cubrir intenciones informacionales y enlazar a servicios/ciudades; no debe absorber landings de ciudad.
- `/no/` se mantiene dentro de la arquitectura internacional.
- Las versiones EN/DE/NL/RU/NO deben crecer solo cuando exista contenido revisado, no por traduccion automatica masiva sin control.

## Prioridad de mejora inicial

Las 5 landings prioritarias para mejorar primero son:

1. `/es/aire-acondicionado-benidorm/`
2. `/es/aire-acondicionado-altea/`
3. `/es/aire-acondicionado-calpe/`
4. `/es/aire-acondicionado-finestrat/`
5. `/es/aire-acondicionado-la-nucia/`

Justificacion: coinciden con demanda comercial local, aparecen ya en la arquitectura viva, tienen variantes en los 6 idiomas y cubren tipologias distintas: apartamentos turisticos, costa/salitre, chalets, obra nueva y viviendas unifamiliares.

## Riesgos SEO principales

- Duplicacion de title y meta description en las 20 landings ES.
- Contenido local demasiado parecido entre ciudades.
- FAQ duplicada o generica en ciudades pequenas.
- Reformas esta enlazado sitewide pero no esta en sitemap y su encaje tematico no esta decidido.
- Ausencia de hubs `/es/servicios/`, `/es/zonas/` y `/es/blog/` crea una arquitectura plana y poco escalable.
- Las rutas de servicio existen como contenido de repo, pero no son rutas vivas.
- Cualquier cambio de URL en ciudades seria innecesario y arriesgado.

## Estado tras Fase P1

Implementado en rama, pendiente de despliegue a dev:

- `/es/servicios/` como hub ES listo con title, description, canonical y BreadcrumbList.
- `/es/zonas/` como hub ES listo con listado de las 20 URLs actuales de ciudad.
- `/es/blog/` como seccion `Guias`, sin articulos publicados ni slugs falsos.
- Las landings P1 ES reciben title/description unicos y un bloque de enlaces a `/es/servicios/`, `/es/zonas/` y zonas cercanas.
- El menu principal ES queda unificado: Inicio, Metodo, Nosotros, Servicios, Zonas, FAQ, Guias y Contacto.
- No se han creado equivalentes EN/DE/NL/RU/NO para los hubs.
- No se ha tocado `/no/`, reformas ni el popup/formulario.
- No se ha modificado `public/sitemap.xml`; los hubs deben anadirse solo despues de validar contenido y despliegue.

## Segunda fase propuesta

1. Desplegar y validar los hubs P1 en dev antes de tocar sitemap.
2. Crear 3 landings de servicio transaccionales iniciales.
3. Publicar 3-5 articulos de blog P1 que enlacen a servicios y ciudades.
4. Decidir el futuro de reformas con datos de Search Console/leads.
5. Replicar mejoras a EN/NO donde haya demanda real, manteniendo `/no/`.
