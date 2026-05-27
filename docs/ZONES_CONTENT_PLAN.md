# Plan de contenido para zonas

Fecha: 2026-05-27  
Alcance: plan editorial y SEO. No implementar paginas nuevas en esta fase.

## Decision

Las ciudades deben ser landings locales/transaccionales, no posts de blog.

Motivos:

- Responden a busquedas con intencion de contratar: `aire acondicionado Benidorm`, `instalacion aire acondicionado Altea`, etc.
- Ya tienen URLs vivas, canonical propio, hreflang, CTA, formulario y WhatsApp.
- Convertirlas en blog diluiria la intencion comercial y crearia riesgo de redireccion/migracion innecesaria.
- El blog debe apoyar a estas landings con contenido informacional, no sustituirlas.

## Estado actual

| Elemento | Estado |
| --- | --- |
| URLs vivas de zona | 20 grupos x 6 idiomas |
| Sitemap | Incluye todas las ciudades, no incluye hub `/es/zonas/` |
| Hub `/es/zonas/` | No existe, 404 en dev |
| Contenido | Estructura correcta pero repetitiva |
| CTA | Correcto: presupuesto, telefono, WhatsApp, formulario |
| Hreflang | Correcto en estructura, incluye `/no/` |
| Riesgo | Titles/descriptions duplicadas y cuerpo base repetido |

## Landings P1 a mejorar primero

| Prioridad | URL | Keyword principal | Motivo | Mejora principal |
| --- | --- | --- | --- | --- |
| P1 | `/es/aire-acondicionado-benidorm/` | aire acondicionado Benidorm | Mayor mercado local y turistico | Apartamentos turisticos, sustituciones rapidas, mantenimiento en temporada |
| P1 | `/es/aire-acondicionado-altea/` | aire acondicionado Altea | Costa, viviendas premium, cascos/urbanizaciones | Salitre, estetica, cascos antiguos, villas |
| P1 | `/es/aire-acondicionado-calpe/` | aire acondicionado Calpe | Costa y apartamentos | Multisplit, salitre, terrazas, comunidades |
| P1 | `/es/aire-acondicionado-finestrat/` | aire acondicionado Finestrat | Obra nueva y Balcon de Finestrat | Preinstalacion, conductos, pisos nuevos |
| P1 | `/es/aire-acondicionado-la-nucia/` | aire acondicionado La Nucia | Chalets y casas unifamiliares | Zonificacion, conductos, casas de dos plantas |

## Modelo recomendado para cada landing de ciudad

Cada ciudad debe tener un bloque unico real, no solo sustitucion del nombre.

| Bloque | Recomendacion |
| --- | --- |
| Title | `Aire acondicionado en {Ciudad}: instalacion, reparacion y mantenimiento | +QUECLIMA` |
| Description | Mencionar ciudad, servicio, tipologia local y CTA |
| H1 | Mantener `Aire acondicionado en {Ciudad}` o ajustar con servicio principal |
| Intro local | 120-180 palabras especificas de ciudad |
| Tipologias | Apartamentos, chalets, locales, comunidades, obra nueva segun ciudad |
| Problemas locales | Salitre, calor, terrazas, falsos techos, ruido, comunidad, orientacion |
| Servicios | Instalacion, mantenimiento, reparacion, bomba de calor |
| Prueba local | Barrios/zonas cercanas, casos, tiempos, experiencia |
| FAQ | 4-6 preguntas con al menos 2 muy especificas de ciudad |
| Enlaces internos | Servicio principal, ciudades cercanas, articulos blog relacionados |
| CTA | Presupuesto, telefono, WhatsApp, formulario/popup preservados |

## Briefs por ciudad P1

### Benidorm

- Enfocar en apartamentos turisticos, comunidades, sustituciones entre huespedes y respuesta en temporada alta.
- Mejorar title/description con intencion local.
- Incluir enlaces a futuro `/es/servicios/instalacion-aire-acondicionado/`, `/es/servicios/mantenimiento-climatizacion/` y blog sobre apartamentos turisticos.
- FAQ sugeridas: potencia para apartamentos, sustitucion rapida, salitre, permisos en comunidad.

### Altea

- Enfocar en viviendas cerca del mar, urbanizaciones, cascos con limitaciones esteticas y villas.
- Mencionar cuidado de fachada, canaletas discretas, soportes antivibracion y mantenimiento por salitre.
- Enlazar a Calpe, Albir y La Nucia como zonas cercanas.
- FAQ sugeridas: equipos en casco antiguo, ubicacion de exterior, salitre, conductos en villas.

### Calpe

- Enfocar en apartamentos de costa, terrazas, comunidades y multisplit.
- Mencionar salitre, ubicacion de unidad exterior y mantenimiento preventivo.
- Enlazar a Altea, Benissa/Moraira si se crean despues, y servicio de mantenimiento.
- FAQ sugeridas: multisplit con poco espacio exterior, garantia, salitre, mantenimiento antes de verano.

### Finestrat

- Enfocar en obra nueva, Balcon de Finestrat, preinstalaciones y conductos.
- Mencionar revision de preinstalacion antes de instalar maquina.
- Enlazar a Benidorm, La Nucia y servicio de conductos/instalacion.
- FAQ sugeridas: preinstalacion en obra nueva, conductos vs split, zonificacion, plazo de instalacion.

### La Nucia

- Enfocar en chalets, casas de dos plantas, zonificacion y bomba de calor.
- Mencionar diseno por zonas dia/noche, eficiencia en invierno y maquinas silenciosas.
- Enlazar a Polop, Alfaz del Pi, Benidorm y servicio de bomba de calor.
- FAQ sugeridas: conductos en chalets, dimensionamiento por plantas, multisplit, bomba de calor en invierno.

## Triage del resto de zonas

| Prioridad | Zonas | Motivo |
| --- | --- | --- |
| P2 | Albir, Alfaz del Pi, Villajoyosa, Polop, Callosa den Sarria | Demanda razonable, contenido con algun FAQ local o cercania a P1 |
| P3 | Beniarda, Benifato, Benimantell, Bolulla, Confrides, Guadalest, Orxeta, Relleu, Sella, Tarbena | Mas riesgo de thin content; mejorar despues o agrupar desde hub |

## Hub futuro `/es/zonas/`

El hub debe crearse como pagina indice, no como sustituto de las landings actuales.

Objetivo:

- Explicar cobertura en Marina Baixa, Costa Blanca norte y Alicante.
- Listar las 20 zonas con enlaces a las URLs actuales.
- Destacar P1 arriba.
- Incluir mapa/cobertura textual, CTA y enlaces a servicios principales.
- No canonicalizar ciudades al hub.
- No mover ciudades bajo `/es/zonas/{ciudad}/`.

## Reglas para no romper SEO

- No borrar ni redirigir URLs actuales de ciudad.
- No convertir ciudades en posts.
- No quitar popup/formulario ni WhatsApp.
- No borrar `/no/`.
- No publicar traducciones internacionales si no estan revisadas.
- No meter contenido blog dentro de una landing de ciudad salvo como enlaces relacionados.
