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

Estado Fase servicios SEO:

- `/es/servicios/` enlaza a cinco paginas de servicio:
  - `/es/servicios/instalacion-aire-acondicionado/`
  - `/es/servicios/mantenimiento-climatizacion/`
  - `/es/servicios/reparacion-aire-acondicionado/`
  - `/es/servicios/aerotermia-bomba-calor/`
  - `/es/servicios/energia-solar-termica/`
- Cada pagina de servicio enlaza de vuelta a `/es/servicios/`, a `/es/zonas/` y a las landings P1 de Benidorm, Altea, Calpe, Finestrat y La Nucia.
- No se crearon equivalentes extranjeros ni se enlazaron rutas extranjeras inexistentes.
- No se anadieron estas rutas al sitemap todavia.

## Tratamiento de `/es/zonas/`

Debe ser hub y listado:

- Enlaza a las 20 URLs actuales de ciudad.
- Destaca Benidorm, Altea, Calpe, Finestrat y La Nucia arriba.
- Incluye enlaces a servicios principales.
- No reemplaza ni canonicaliza las ciudades.

## Tratamiento de `/es/servicios/`

Debe ser hub transaccional:

- Enlaza a los cinco servicios individuales creados en espanol.
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

## Estado multiidioma post-correccion 2026-05-28

- Homes EN/DE/NL/RU/NO enlazan en el cuerpo a sus hubs reales de servicios, zonas/areas y guias.
- El selector de idioma conserva equivalencias de home, hubs, paginas de servicio y landings locales de aire acondicionado cuando existen.
- Footer queda como navegacion limpia de 3 enlaces por idioma: servicios, zonas/areas y guias.
- No se crean equivalencias de articulos de guia porque todavia no existen articulos publicados.
- Si una landing local no existe en el idioma destino, la estrategia de fallback es el hub de zonas/areas de ese idioma.

## Auditoria final de enlaces - 2026-05-28

Referencia completa: `docs/FINAL_PREDEPLOY_AUDIT.md`.

Resultado:

- 36 paginas fuente auditadas.
- 195 enlaces internos unicos comprobados.
- 1 enlace interno roto: `/politica-de-cookies` devuelve 404 y aparece en el banner de cookies.

Decision:

- No se crea pagina nueva ni se cambia URL durante esta auditoria.
- Debe resolverse antes de Nicalia con pagina legal/restauracion de URL o destino aprobado.
