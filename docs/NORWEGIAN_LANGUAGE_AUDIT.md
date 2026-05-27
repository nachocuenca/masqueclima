# Auditoria idioma noruego `/no/`

## Estado produccion

- `/no/` devuelve `200`.
- `/no/` esta en el sitemap vivo.
- Noruego aparece como `hreflang="no"` y ruta `/no/`.
- El sitemap contiene `21` URLs noruegas.
- Produccion incluye `/no/oppussing-benidorm/` como reformas viva fuera del sitemap.
- El selector de idiomas enlaza noruego en homes, landings y reformas.

## Decision

- Se mantiene la ruta `/no/`.
- Se mantiene `hreflang="no"` para respetar produccion y URLs existentes.
- Se anadio `og:locale` fallback `nb_NO` para helpers legacy.
- Se preservan textos noruegos de produccion mediante snapshots; `app/translations/no.php` queda como fallback para vistas internas/404.

## URLs noruegas preservadas

- `https://masqueclima.es/no/`
- `https://masqueclima.es/no/aircondition-benidorm/`
- `https://masqueclima.es/no/aircondition-la-nucia/`
- `https://masqueclima.es/no/aircondition-alfaz-del-pi/`
- `https://masqueclima.es/no/aircondition-albir/`
- `https://masqueclima.es/no/aircondition-altea/`
- `https://masqueclima.es/no/aircondition-calpe/`
- `https://masqueclima.es/no/aircondition-finestrat/`
- `https://masqueclima.es/no/aircondition-villajoyosa/`
- `https://masqueclima.es/no/aircondition-polop/`
- `https://masqueclima.es/no/aircondition-callosa-den-sarria/`
- `https://masqueclima.es/no/aircondition-guadalest/`
- `https://masqueclima.es/no/aircondition-beniarda/`
- `https://masqueclima.es/no/aircondition-benimantell/`
- `https://masqueclima.es/no/aircondition-benifato/`
- `https://masqueclima.es/no/aircondition-confrides/`
- `https://masqueclima.es/no/aircondition-bolulla/`
- `https://masqueclima.es/no/aircondition-tarbena/`
- `https://masqueclima.es/no/aircondition-orxeta/`
- `https://masqueclima.es/no/aircondition-relleu/`
- `https://masqueclima.es/no/aircondition-sella/`

## Pendiente posterior

- Revisar Search Console/analytics antes de decidir si conviene evolucionar hreflang a `nb` o mantener `no` indefinidamente.
