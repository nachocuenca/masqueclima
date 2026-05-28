# Heroes de localidades — Masqueclima

## Carpeta de destino

```
public/assets/img/localidades/heroes/
```

## Mapeo localidad → archivo

| Slug | Archivo WebP |
|---|---|
| albir | hero-localidad-albir.webp |
| alfaz-del-pi | hero-localidad-alfaz-del-pi.webp |
| altea | hero-localidad-altea.webp |
| beniarda | hero-localidad-beniarda.webp |
| benidorm | hero-localidad-benidorm.webp |
| benifato | hero-localidad-benifato.webp |
| benimantell | hero-localidad-benimantell.webp |
| bolulla | hero-localidad-bolulla.webp |
| callosa-den-sarria | hero-localidad-callosa-den-sarria.webp |
| calpe | hero-localidad-calpe.webp |
| confrides | hero-localidad-confrides.webp |
| finestrat | hero-localidad-finestrat.webp |
| guadalest | hero-localidad-guadalest.webp |
| la-nucia | hero-localidad-la-nucia.webp |
| orxeta | hero-localidad-orxeta.webp |
| polop | hero-localidad-polop.webp |
| relleu | hero-localidad-relleu.webp |
| sella | hero-localidad-sella.webp |
| tarbena | hero-localidad-tarbena.webp |
| villajoyosa | hero-localidad-villajoyosa.webp |

## Páginas donde se conectan

URLs de las 20 páginas de localidad (rutas existentes, no nuevas):

```
/es/aire-acondicionado-albir/
/es/aire-acondicionado-alfaz-del-pi/
/es/aire-acondicionado-altea/
/es/aire-acondicionado-beniarda/
/es/aire-acondicionado-benidorm/
/es/aire-acondicionado-benifato/
/es/aire-acondicionado-benimantell/
/es/aire-acondicionado-bolulla/
/es/aire-acondicionado-callosa-den-sarria/
/es/aire-acondicionado-calpe/
/es/aire-acondicionado-confrides/
/es/aire-acondicionado-finestrat/
/es/aire-acondicionado-guadalest/
/es/aire-acondicionado-la-nucia/
/es/aire-acondicionado-orxeta/
/es/aire-acondicionado-polop/
/es/aire-acondicionado-relleu/
/es/aire-acondicionado-sella/
/es/aire-acondicionado-tarbena/
/es/aire-acondicionado-villajoyosa/
```

## Sistema de patching

Las páginas de localidad se sirven desde snapshots estáticos en `app/snapshots/es__aire-acondicionado-{slug}.html`. El hero se inyecta en runtime por:

- `locality_hero_map(): array` — devuelve el mapa slug → path del WebP.
- `patch_locality_hero_image(string $html, string $path, string $lang): string` — detecta la ruta `/es/aire-acondicionado-{slug}/`, busca el hero en el mapa, reemplaza el atributo `src` del `<img class="hero-img">` y actualiza los preloads.

Ambas funciones están en `app/front_controller.php`. La función se llama desde `patch_snapshot_html()` después de `patch_es_p1_location_page()`.

## Fallback

Si el archivo WebP no existe en disco (`public_asset_exists()` devuelve `false`), el snapshot conserva su imagen original sin cambios. No hay ruptura de página.

## Dimensiones de las imágenes

- **Resolución:** 2560×1440 (16:9)
- **Formato:** WebP
- **Tamaño:** 208–497 KB por archivo
- **Fuente:** ZIP `masqueclima_localidad_heroes_final_20de20.zip`

## Object-position

Valor inicial: `center center` para todas las localidades (suficiente para las imágenes de paisaje mediterráneo). Si alguna imagen requiere ajuste de encuadre, añadir un campo `image_position` por slug en `locality_hero_map()` y propagarlo al atributo `style` del img.

## Estado

- ✅ 20 WebP instalados en `public/assets/img/localidades/heroes/`
- ✅ Patching implementado en `app/front_controller.php`
- ✅ Lint PHP: 0 errores
- ⚠️ **No añadir al sitemap hasta validar visualmente en dev**
- ⚠️ **No hacer commit hasta validar en el entorno de desarrollo**

## Notas sobre snapshots

18 de los 20 snapshots tenían `src="/assets/img/hero1.jpg"` genérico.
Benidorm ya tenía `src="/assets/img/hero-benidorm.webp"` y Guadalest tenía un custom anterior.
El patching sobreescribe en runtime cualquier src anterior sin modificar los archivos snapshot.
