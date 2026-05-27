# Plan de enlazado interno

Fecha: 2026-05-27  
Alcance: arquitectura de enlaces propuesta. No aplicar todavia.

## Estado actual

- Las ciudades enlazan entre si de forma amplia: cada landing ES contiene enlaces a las 20 URLs ES de ciudad.
- Reformas esta enlazado desde la navegacion de los snapshots.
- No existen hubs vivos `/es/servicios/`, `/es/zonas/` ni `/es/blog/`.
- Las paginas de servicio y guias existen como contenido/vistas en repo, pero no como rutas vivas en dev.
- Las paginas preservan popup/formulario y WhatsApp.

## Objetivo

Crear una arquitectura clara sin mover las URLs existentes:

```text
Home idioma
  -> Servicios hub
  -> Zonas hub
  -> Blog hub
  -> Landings P1

Servicios hub
  -> Servicios individuales
  -> Landings de zona relevantes
  -> Articulos blog relacionados

Zonas hub
  -> URLs actuales de ciudad
  -> Servicios principales

Blog hub
  -> Articulos informacionales
  -> Servicios y ciudades segun intencion
```

## Reglas de enlazado

| Origen | Enlazar a | Motivo |
| --- | --- | --- |
| `/es/` | `/es/servicios/`, `/es/zonas/`, `/es/blog/`, P1 ciudades | Orientar arquitectura desde home |
| `/es/servicios/` | Servicios individuales y P1 ciudades | Convertir demanda transaccional |
| Servicio individual | Ciudades donde mas aplica y blog relacionado | Relevancia servicio + local |
| `/es/zonas/` | 20 ciudades actuales | Hub de cobertura sin cambiar URLs |
| Ciudad | Servicio principal, ciudades cercanas, blog relacionado | Mejorar contexto y conversion |
| Blog | 1 servicio + 1-3 ciudades | Pasar autoridad informacional a transaccional |
| Reformas | Pendiente | No cambiar hasta decision |

## Enlaces recomendados por landing P1

| Landing | Enlaces a servicios | Enlaces a blog | Enlaces a zonas cercanas |
| --- | --- | --- | --- |
| Benidorm | Instalacion, mantenimiento, reparacion | Apartamento turistico, precios, potencia | Finestrat, La Nucia, Albir |
| Altea | Instalacion, mantenimiento | Salitre/costa, comunidad vecinos, conductos vs split | Albir, Calpe, La Nucia |
| Calpe | Instalacion, mantenimiento | Salitre/costa, multisplit, marcas | Altea, Villajoyosa si aplica, Finestrat |
| Finestrat | Instalacion, conductos, bomba de calor | Conductos vs split, potencia, obra nueva | Benidorm, La Nucia, Polop |
| La Nucia | Instalacion, bomba de calor, mantenimiento | Conductos vs split, bomba de calor, potencia | Polop, Alfaz del Pi, Benidorm |

## Anchor text recomendado

Usar anchors descriptivos y naturales:

- `instalacion de aire acondicionado`
- `mantenimiento de aire acondicionado`
- `reparacion de aire acondicionado`
- `aire acondicionado en Benidorm`
- `aire acondicionado en Altea`
- `calcular la potencia necesaria`
- `mantenimiento en zonas de costa`

Evitar anchors repetidos en exceso tipo `haz clic aqui` o listas masivas sin contexto.

## Sitemap y navegacion

Cuando se creen rutas nuevas:

- Solo anadir al sitemap paginas publicadas, indexables y con contenido suficiente.
- No anadir borradores ni paginas vacias.
- Mantener hreflang solo cuando existan equivalentes reales.
- No quitar `/no/` del selector ni del sitemap.
- Mantener canonicals absolutos a la URL final de produccion.

## Tratamiento de `/es/zonas/`

Debe ser hub y listado:

- Enlaza a las 20 URLs actuales de ciudad.
- Destaca Benidorm, Altea, Calpe, Finestrat y La Nucia arriba.
- Incluye enlaces a servicios principales.
- No reemplaza ni canonicaliza las ciudades.

## Tratamiento de `/es/servicios/`

Debe ser hub transaccional:

- Enlaza a servicios individuales.
- Enlaza a P1 ciudades con texto contextual.
- Debe tener CTA principal a presupuesto y WhatsApp.
- No debe intentar posicionar por todas las ciudades.

## Tratamiento de `/es/blog/`

Debe ser hub informacional:

- Enlaza a articulos por categorias o temas.
- Cada articulo enlaza hacia servicio/ciudad.
- No se deben crear posts con la misma intencion que una landing local.

## Reformas

Hasta decidir su futuro:

- No borrar.
- No redirigir.
- No quitar `/no/`.
- No anadir al sitemap sin decision.
- Revisar si conviene mantener enlace sitewide o moverlo a una zona menos prominente en fase posterior.
