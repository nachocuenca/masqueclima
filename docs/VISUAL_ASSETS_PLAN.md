# Visual Assets Plan

Fecha: 2026-05-28  
Alcance: capa visual preparada para hubs, paginas SEO de servicios y modulos reutilizables en la web legacy PHP.

## Estructura de carpetas

Base:

```text
public/assets/img/
  heroes/
  services/
  guides/
  zones/
```

Uso recomendado:

- `heroes/`: imagenes panoramicas para heroes compactos de hubs y paginas de servicio.
- `services/`: imagenes de apoyo para modulos de contenido de servicios.
- `guides/`: imagenes futuras para Guias cuando existan articulos reales.
- `zones/`: imagenes o mapas visuales de zonas, costa, Marina Baixa y Costa Blanca.

## Convencion de nombres

- Formato: `contexto-descriptivo.webp`.
- Minusculas, guiones medios, sin espacios, sin tildes.
- No usar nombres genericos como `image1.webp` o `banner-final.webp`.
- Mantener el mismo concepto entre hero y modulo cuando aplique:
  - Hero: `service-instalacion-aire-acondicionado.webp`
  - Modulo: `module-instalacion.webp`

## Recomendaciones tecnicas

| Tipo | Carpeta | Proporcion | Tamano recomendado | Formato |
| --- | --- | --- | --- | --- |
| Hero compacto | `heroes/` | 16:9 | 1920x1080 o 2400x1350 | WebP |
| Modulo contenido | `services/`, `zones/`, `guides/` | 4:3 o 3:2 | 1200x900 o 1400x933 | WebP |
| Imagen de tarjeta futura | `guides/` | 4:3 | 1000x750 | WebP |
| Mapa/visual zona | `zones/` | 16:10 o SVG inline | 1400x875 | WebP/SVG |

El peso objetivo por imagen debe mantenerse razonable:

- Heroes: idealmente por debajo de 250-350 KB.
- Modulos: idealmente por debajo de 180-250 KB.

## Slots actuales de hero

### Hubs

| Pagina | Archivo sugerido | Uso | Proporcion | Tipo |
| --- | --- | --- | --- | --- |
| `/es/servicios/` | `public/assets/img/heroes/hub-servicios-climatizacion.webp` | Hero de servicios | 16:9 | Hero |
| `/es/zonas/` | `public/assets/img/heroes/hub-zonas-marina-baixa.webp` | Hero de zonas/cobertura | 16:9 | Hero |
| `/es/blog/` | `public/assets/img/heroes/hub-guias-climatizacion.webp` | Hero de Guias | 16:9 | Hero |

### Servicios

| Pagina | Archivo sugerido | Uso | Proporcion | Tipo |
| --- | --- | --- | --- | --- |
| `/es/servicios/instalacion-aire-acondicionado/` | `public/assets/img/services/service-instalacion-aire-acondicionado.webp` | Hero de instalacion | 16:9 | Hero |
| `/es/servicios/mantenimiento-climatizacion/` | `public/assets/img/services/service-mantenimiento-climatizacion.webp` | Hero de mantenimiento | 16:9 | Hero |
| `/es/servicios/reparacion-aire-acondicionado/` | `public/assets/img/services/service-reparacion-aire-acondicionado.webp` | Hero de reparacion | 16:9 | Hero |
| `/es/servicios/aerotermia-bomba-calor/` | `public/assets/img/services/service-aerotermia-bomba-calor.webp` | Hero de aerotermia/bomba de calor | 16:9 | Hero |
| `/es/servicios/energia-solar-termica/` | `public/assets/img/services/service-energia-solar-termica.webp` | Hero de energia solar termica | 16:9 | Hero |

## Slots de modulos reutilizables

| Slot | Archivo sugerido | Uso | Proporcion | Tipo |
| --- | --- | --- | --- | --- |
| Imagen de instalacion | `public/assets/img/services/module-instalacion.webp` | Modulo de instalacion | 4:3 | Contenido |
| Imagen de mantenimiento | `public/assets/img/services/module-mantenimiento.webp` | Modulo de mantenimiento | 4:3 | Contenido |
| Imagen de reparacion/diagnostico | `public/assets/img/services/module-reparacion.webp` | Modulo de averias/diagnostico | 4:3 | Contenido |
| Imagen de aerotermia/bomba de calor | `public/assets/img/services/module-aerotermia.webp` | Modulo de aerotermia | 4:3 | Contenido |
| Imagen de solar termica | `public/assets/img/services/module-solar-termica.webp` | Modulo de solar termica | 4:3 | Contenido |
| Vivienda/local/comunidad | `public/assets/img/services/module-contexto-vivienda.webp` | Contexto general para servicios | 3:2 | Contenido |

## Soporte en plantilla

### Heroes

El partial `views/partials/hub_hero.php` acepta:

- `hero_image`
- `hero_alt`
- `visual_slot`
- `overlay`
- `image_position`

Si el archivo configurado existe en `public/assets/`, se usa como imagen del hero. Si todavia no existe, se usa la imagen legacy de fallback (`/assets/img/hero1.webp`). Si tampoco existiera, se muestra un fondo visual generado por CSS para no romper el layout.

Paginas conectadas actualmente:

- `/es/servicios/`
- `/es/zonas/`
- `/es/blog/`
- Las cinco paginas SEO de servicios.

## Pack WebP conectado

Heroes conectados:

- `/es/servicios/` -> `heroes/hub-servicios-climatizacion.webp`
- `/es/zonas/` -> `heroes/hub-zonas-marina-baixa.webp`
- `/es/blog/` -> `heroes/hub-guias-climatizacion.webp`
- Instalacion -> `services/service-instalacion-aire-acondicionado.webp`
- Mantenimiento -> `services/service-mantenimiento-climatizacion.webp`
- Reparacion -> `services/service-reparacion-aire-acondicionado.webp`
- Aerotermia -> `services/service-aerotermia-bomba-calor.webp`
- Solar termica -> `services/service-energia-solar-termica.webp`

Object-position inicial:

- Hubs: `center center`
- Instalacion: `right center`
- Mantenimiento: `right center`
- Reparacion: `right center`
- Aerotermia: `left center`
- Solar termica: `right center`

Modulos internos activados en paginas de servicio:

- Instalacion -> `services/module-instalacion.webp`
- Mantenimiento -> `services/module-mantenimiento.webp`
- Reparacion -> `services/module-reparacion.webp`
- Aerotermia -> `services/module-aerotermia.webp`
- Solar termica -> `services/module-solar-termica.webp`

Imagenes pendientes de conectar (presentes en repo, sin asignar aun):

- `guides/guias-climatizacion-costa-blanca.webp` — candidata a modulo visual en la seccion de guias o futura landing de guias individual.
- `zones/marina-baixa-costa-blanca.webp` — candidata a modulo visual en la seccion de zonas o junto al mapa SVG.

## Limpieza de assets (2026-05-28)

Imagenes sin referencias en el codigo movidas a `public/assets/img/_archive/legacy-20260525/`:

- `hero-benidorm.webp`
- `hero-guadalest.webp`
- `especialista-limpia-y-repara-el-aire-acondicionado-de-pared.jpg`
- `cases/altea-conductos.jpg`
- `cases/atico-benidorm.jpg`
- `cases/oficina-albir.jpg`
- `0_Air_Conditioner_Remote_Control_3840x2160.mp4`
- `services/module-contexto-vivienda.webp`

Se mantienen en produccion (usadas activamente):

- `hero1.webp`, `hero1.jpg`, `hero.jpg`, `hero.mp4` — home.php
- `og.jpg` — layout (Open Graph)
- `favicon.ico`, `favicon-32x32.png` — layout
- `flags/*.svg` — header selector de idioma
- `masqueclimalogo_.png` — header logo
- `Asesoramiento.png`, `Venta.png`, `Instalacion.png`, `postVenta.png` — home iconos
- `WhatsApp.png` — home boton WhatsApp
- Logos de marcas (Daikin, Mitsubishi, Fujitsu, Panasonic, Haier, LG, Gree, Giatsu) — home carrusel
- Reparacion -> `services/module-reparacion.webp`
- Aerotermia -> `services/module-aerotermia.webp`
- Solar termica -> `services/module-solar-termica.webp`

Quedan disponibles sin usar en esta fase:

- `services/module-contexto-vivienda.webp`
- `zones/marina-baixa-costa-blanca.webp`
- `guides/guias-climatizacion-costa-blanca.webp`

### Modulo imagen + texto

Se creo `views/partials/image_text_block.php`.

Uso previsto:

- Bloques futuros de servicios con imagen + explicacion.
- Guias cuando existan articulos reales.
- Modulos de zonas o casos de uso.

El componente soporta:

- Imagen con proporcion estable.
- Fallback visual si falta imagen.
- Variante invertida con `reverse`.
- Caption opcional.
- Layout responsive sin JS.

## Criterio visual

- Imagenes reales, limpias y relacionadas con climatizacion.
- Evitar fotos genericas de banco que no parezcan servicio local.
- Evitar imagenes oscuras, borrosas o demasiado abstractas.
- Priorizar equipos, instalacion real, vivienda/local/comunidad, costa y contexto Marina Baixa.
- No usar imagenes de reformas en esta web.
- No usar imagenes que prometan tecnologia o instalaciones que no se ofrecen.

## Siguiente paso recomendado

1. Generar o seleccionar las 8 imagenes de hero.
2. Optimizar a WebP y revisar peso.
3. Subirlas con los nombres documentados.
4. Revisar en local desktop y movil.
5. Valorar insertar modulos `image_text_block` en servicios cuando las imagenes de apoyo esten listas.
6. Mantener sitemap sin cambios hasta que la fase visual y SEO se valide.
