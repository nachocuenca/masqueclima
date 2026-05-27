# Notas de limpieza de copy visible

Fecha: 2026-05-27  
Rama: `fix/legacy-php-dev-stabilization`

## Objetivo

Refinar la Fase P1 para que las paginas nuevas suenen a negocio local de climatizacion y no a auditoria SEO, prototipo o documento interno.

## Paginas revisadas

- `/es/`
- `/es/servicios/`
- `/es/zonas/`
- `/es/blog/`
- `/es/aire-acondicionado-benidorm/`
- `/es/aire-acondicionado-altea/`
- `/es/aire-acondicionado-calpe/`
- `/es/aire-acondicionado-finestrat/`
- `/es/aire-acondicionado-la-nucia/`

## Terminos eliminados del copy visible

Se valido localmente que no aparezcan en texto visible:

- `landing`
- `hub`
- `P1`
- `URL`
- `indexable`
- `contenido completo`
- `arquitectura`
- `preservadas`
- `canonicas`
- `sitemap`
- `editorial preparado`
- `se publicaran solo cuando`
- `contenido fino`
- `placeholder`
- `proximas guias`

## Copy final de paginas nuevas

### `/es/servicios/`

Enfoque: servicios reales de climatizacion.

H1: `Servicios de climatizacion en Benidorm y Marina Baixa`

Subtitulo: instalacion, mantenimiento y reparacion de aire acondicionado, calefaccion y energia para viviendas, apartamentos turisticos, comunidades y negocios.

Servicios visibles:

- Instalacion de aire acondicionado
- Mantenimiento de climatizacion
- Reparacion de aire acondicionado
- Calefaccion y bomba de calor
- Energia solar termica

### `/es/zonas/`

Enfoque: cobertura local.

H1: `Servicio de climatizacion por zonas en Alicante`

Subtitulo: trabajo desde Benidorm para Marina Baixa, Costa Blanca norte y provincia de Alicante, con desplazamiento rapido y asesoramiento cercano.

Listado: localidades donde se realizan instalaciones, mantenimiento y reparaciones de climatizacion.

### `/es/blog/`

Enfoque: `Guias`, no blog vacio.

H1: `Guias de climatizacion y aire acondicionado`

Subtitulo: consejos practicos para elegir, mantener y aprovechar mejor el sistema de climatizacion en la Costa Blanca.

No se crean articulos, slugs falsos ni contenido fino. La pagina queda accesible, pero fuera del sitemap hasta publicar guias reales.

## Navegacion

Menu ES visible:

- Inicio
- Metodo
- Nosotros
- Servicios
- Zonas
- FAQ
- Guias
- Contacto

No aparecen en el menu principal:

- Ciudades
- Reformas integrales
- Blog como etiqueta

## Pendiente

- Repetir validacion tras despliegue a `https://dev.masqueclima.es`.
- Crear articulos reales antes de anadir `/es/blog/` al sitemap.
- Extraer hubs a vistas en una fase tecnica posterior.
