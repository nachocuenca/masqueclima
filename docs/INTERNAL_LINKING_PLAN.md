# Plan de enlazado interno

Fecha: 2026-05-27  
Alcance: arquitectura de enlaces P1 aplicada en legacy PHP y decisiones pendientes.

## Estado actual

- Las ciudades enlazan entre si de forma amplia: cada landing ES contiene enlaces a las 20 URLs ES de ciudad.
- Reformas queda fuera del header y del footer principal.
- Existen hubs vivos en espanol: `/es/servicios/`, `/es/zonas/` y `/es/blog/`.
- Servicios, Zonas y Guias no cargan el header; se enlazan desde footer ES y desde contexto.
- Las paginas preservan popup/formulario y WhatsApp.

## Objetivo

Crear una arquitectura clara sin mover las URLs existentes:

```text
Home idioma
  -> Servicios hub
  -> Zonas hub
  -> Guias
  -> Landings P1

Servicios hub
  -> Servicios individuales
  -> Landings de zona relevantes
  -> Guias relacionadas

Zonas hub
  -> URLs actuales de ciudad
  -> Servicios principales

Guias
  -> Contenido informacional
  -> Servicios y ciudades segun intencion
```

## Reglas de enlazado

| Origen | Enlazar a | Motivo |
| --- | --- | --- |
| `/es/` | `/es/servicios/`, `/es/zonas/`, `/es/blog/`, P1 ciudades | Orientar arquitectura desde enlaces contextuales y footer |
| `/es/servicios/` | Servicios individuales y P1 ciudades | Convertir demanda transaccional |
| Servicio individual | Ciudades donde mas aplica y guias relacionadas | Relevancia servicio + local |
| `/es/zonas/` | 20 ciudades actuales | Hub de cobertura sin cambiar URLs |
| Ciudad | Servicio principal, ciudades cercanas, guias relacionadas | Mejorar contexto y conversion |
| Guias | 1 servicio + 1-3 ciudades | Pasar autoridad informacional a transaccional |
| Reformas | Pendiente | No cambiar hasta decision |

## Enlaces recomendados por landing P1

| Landing | Enlaces a servicios | Enlaces a guias | Enlaces a zonas cercanas |
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

Estado Fase P1:

- Los hubs `/es/servicios/`, `/es/zonas/` y `/es/blog/` se sirven en espanol desde el front controller.
- No se anadieron al sitemap en esta fase.
- Las landings P1 enlazan hacia `/es/servicios/` y `/es/zonas/`.
- El indice `/es/blog/` se muestra como Guias y no enlaza a articulos inexistentes; muestra temas sin URLs publicables.
- El header principal ES queda como Inicio, Metodo, Nosotros, Zona, FAQ y Contacto.
- Servicios, Zonas y Guias quedan fuera del header principal y se muestran en el footer ES.
- Ciudades y reformas quedan fuera del header principal; `/es/blog/` se muestra como Guias en footer y no entra en sitemap.
- La home enlaza de forma contextual a servicios, zonas y guias desde Metodo, Zona y FAQ.

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

Debe ser seccion de Guias:

- Enlazara a guias por categorias o temas cuando existan.
- Cada guia enlazara hacia servicio/ciudad cuando sea natural.
- No se deben crear contenidos con la misma intencion que una landing local.

## Reformas

Hasta decidir su futuro:

- No borrar.
- No redirigir.
- No quitar `/no/`.
- No anadir al sitemap sin decision.
- Mantener fuera de header y footer principal hasta decision comercial.
