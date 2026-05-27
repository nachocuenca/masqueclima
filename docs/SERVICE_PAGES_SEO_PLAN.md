# Service Pages SEO Plan

Fecha: 2026-05-28  
Alcance: paginas SEO de servicios en espanol para Masqueclima legacy PHP.

## Arquitectura aplicada

Las paginas de servicio se sirven desde el legacy PHP sin crear snapshots nuevos:

- Contenido: `app/content/services/es.php`
- Vista comun: `views/service_detail.php`
- Routing ligero: `app/front_controller.php`
- Estilos compartidos: `views/partials/hub_styles.php`
- CTA final reutilizable: `views/partials/final_budget_cta.php`

El controlador detecta si la ruta pertenece a un servicio, carga la configuracion y renderiza la vista comun dentro del shell legacy existente.

## URLs creadas

| URL | H1 |
| --- | --- |
| `/es/servicios/instalacion-aire-acondicionado/` | Instalacion de aire acondicionado en Benidorm y Marina Baixa |
| `/es/servicios/mantenimiento-climatizacion/` | Mantenimiento de climatizacion en Benidorm y Marina Baixa |
| `/es/servicios/reparacion-aire-acondicionado/` | Reparacion de aire acondicionado en Benidorm y Marina Baixa |
| `/es/servicios/aerotermia-bomba-calor/` | Aerotermia y bomba de calor en Benidorm y Marina Baixa |
| `/es/servicios/energia-solar-termica/` | Energia solar termica en Benidorm y Marina Baixa |

## Titles y meta descriptions

| URL | Title SEO | Meta description |
| --- | --- | --- |
| `/es/servicios/instalacion-aire-acondicionado/` | Instalacion de aire acondicionado en Benidorm y Marina Baixa \| +QUECLIMA | Instalacion de aire acondicionado en Benidorm y Marina Baixa para viviendas, apartamentos turisticos y locales. Visita previa y presupuesto claro. |
| `/es/servicios/mantenimiento-climatizacion/` | Mantenimiento de climatizacion en Benidorm y Marina Baixa \| +QUECLIMA | Mantenimiento de climatizacion en Benidorm y Marina Baixa: limpieza, revision de equipos, desagues, unidades exteriores y preparacion para verano. |
| `/es/servicios/reparacion-aire-acondicionado/` | Reparacion de aire acondicionado en Benidorm y Marina Baixa \| +QUECLIMA | Reparacion de aire acondicionado en Benidorm y Marina Baixa: equipos que no enfrian, pierden agua, hacen ruido o muestran errores. |
| `/es/servicios/aerotermia-bomba-calor/` | Aerotermia y bomba de calor en Benidorm y Marina Baixa \| +QUECLIMA | Aerotermia y bomba de calor en Benidorm y Marina Baixa para calefaccion eficiente, climatizacion y proyectos energeticos con estudio previo. |
| `/es/servicios/energia-solar-termica/` | Energia solar termica en Benidorm y Marina Baixa \| +QUECLIMA | Energia solar termica en Benidorm y Marina Baixa para agua caliente sanitaria y apoyo energetico en viviendas y negocios. |

## Criterio de contenido

Cada pagina incluye:

- Hero compacto con CTA a presupuesto y WhatsApp.
- Introduccion especifica del servicio.
- Bloque de puntos principales.
- Bloque de situaciones en las que conviene pedir el servicio.
- Proceso de trabajo.
- Zonas donde se presta servicio.
- FAQ breve cuando aporta claridad.
- Enlaces internos a servicios, zonas y landings P1.
- Cierre final con "Solicita tu presupuesto".

El contenido evita promesas no verificables y explica limitaciones cuando procede, especialmente en aerotermia y solar termica.

## Enlazado interno

Desde `/es/servicios/`, cada card enlaza a su pagina especifica:

- Instalacion -> `/es/servicios/instalacion-aire-acondicionado/`
- Mantenimiento -> `/es/servicios/mantenimiento-climatizacion/`
- Reparacion -> `/es/servicios/reparacion-aire-acondicionado/`
- Bomba de calor -> `/es/servicios/aerotermia-bomba-calor/`
- Solar termica -> `/es/servicios/energia-solar-termica/`

Cada pagina de servicio enlaza a:

- `/es/servicios/`
- `/es/zonas/`
- `/es/aire-acondicionado-benidorm/`
- `/es/aire-acondicionado-altea/`
- `/es/aire-acondicionado-calpe/`
- `/es/aire-acondicionado-finestrat/`
- `/es/aire-acondicionado-la-nucia/`

## SEO tecnico

- Canonical absoluto a `https://masqueclima.es` + ruta.
- Title unico por pagina.
- Meta description unica por pagina.
- H1 unico por pagina.
- Sin hreflang extranjero inventado.
- JSON-LD `BreadcrumbList` y `Service` en paginas de servicio.
- Dev mantiene noindex por cabecera/meta del entorno.
- Sitemap pendiente: no se modifica en esta fase.

## Relacion con Guias

Las Guias siguen siendo una seccion separada y no se han creado articulos reales en esta fase. En una fase posterior, cada guia deberia enlazar a uno de estos servicios cuando la intencion lo justifique.

## Relacion con Zonas

Las paginas de servicio refuerzan la cobertura local enlazando a `/es/zonas/` y a las cinco landings P1. Las landings de ciudad no se convierten en articulos ni cambian de URL.

## Pendiente

- Decidir entrada en sitemap tras revision final.
- Valorar enlaces desde landings P1 hacia servicios especificos.
- Valorar traducciones solo cuando exista contenido real equivalente.
- Crear guias reales cuando haya contenido util y completo.
