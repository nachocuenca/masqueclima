# Auditoria de zonas y landings

Produccion tiene `20` grupos de zona en seis idiomas: `120` URLs de zona preservadas.

| Zona | ES | EN | DE | NL | RU | NO |
| --- | --- | --- | --- | --- | --- | --- |
| `albir` | `/es/aire-acondicionado-albir/` | `/en/air-conditioning-albir/` | `/de/klimaanlage-albir/` | `/nl/airco-albir/` | `/ru/konditsioner-albir/` | `/no/aircondition-albir/` |
| `alfaz-del-pi` | `/es/aire-acondicionado-alfaz-del-pi/` | `/en/air-conditioning-alfaz-del-pi/` | `/de/klimaanlage-alfaz-del-pi/` | `/nl/airco-alfaz-del-pi/` | `/ru/konditsioner-alfaz-del-pi/` | `/no/aircondition-alfaz-del-pi/` |
| `altea` | `/es/aire-acondicionado-altea/` | `/en/air-conditioning-altea/` | `/de/klimaanlage-altea/` | `/nl/airco-altea/` | `/ru/konditsioner-altea/` | `/no/aircondition-altea/` |
| `beniarda` | `/es/aire-acondicionado-beniarda/` | `/en/air-conditioning-beniarda/` | `/de/klimaanlage-beniarda/` | `/nl/airco-beniarda/` | `/ru/konditsioner-beniarda/` | `/no/aircondition-beniarda/` |
| `benidorm` | `/es/aire-acondicionado-benidorm/` | `/en/air-conditioning-benidorm/` | `/de/klimaanlage-benidorm/` | `/nl/airco-benidorm/` | `/ru/konditsioner-benidorm/` | `/no/aircondition-benidorm/` |
| `benifato` | `/es/aire-acondicionado-benifato/` | `/en/air-conditioning-benifato/` | `/de/klimaanlage-benifato/` | `/nl/airco-benifato/` | `/ru/konditsioner-benifato/` | `/no/aircondition-benifato/` |
| `benimantell` | `/es/aire-acondicionado-benimantell/` | `/en/air-conditioning-benimantell/` | `/de/klimaanlage-benimantell/` | `/nl/airco-benimantell/` | `/ru/konditsioner-benimantell/` | `/no/aircondition-benimantell/` |
| `bolulla` | `/es/aire-acondicionado-bolulla/` | `/en/air-conditioning-bolulla/` | `/de/klimaanlage-bolulla/` | `/nl/airco-bolulla/` | `/ru/konditsioner-bolulla/` | `/no/aircondition-bolulla/` |
| `callosa-den-sarria` | `/es/aire-acondicionado-callosa-den-sarria/` | `/en/air-conditioning-callosa-den-sarria/` | `/de/klimaanlage-callosa-den-sarria/` | `/nl/airco-callosa-den-sarria/` | `/ru/konditsioner-callosa-den-sarria/` | `/no/aircondition-callosa-den-sarria/` |
| `calpe` | `/es/aire-acondicionado-calpe/` | `/en/air-conditioning-calpe/` | `/de/klimaanlage-calpe/` | `/nl/airco-calpe/` | `/ru/konditsioner-calpe/` | `/no/aircondition-calpe/` |
| `confrides` | `/es/aire-acondicionado-confrides/` | `/en/air-conditioning-confrides/` | `/de/klimaanlage-confrides/` | `/nl/airco-confrides/` | `/ru/konditsioner-confrides/` | `/no/aircondition-confrides/` |
| `finestrat` | `/es/aire-acondicionado-finestrat/` | `/en/air-conditioning-finestrat/` | `/de/klimaanlage-finestrat/` | `/nl/airco-finestrat/` | `/ru/konditsioner-finestrat/` | `/no/aircondition-finestrat/` |
| `guadalest` | `/es/aire-acondicionado-guadalest/` | `/en/air-conditioning-guadalest/` | `/de/klimaanlage-guadalest/` | `/nl/airco-guadalest/` | `/ru/konditsioner-guadalest/` | `/no/aircondition-guadalest/` |
| `la-nucia` | `/es/aire-acondicionado-la-nucia/` | `/en/air-conditioning-la-nucia/` | `/de/klimaanlage-la-nucia/` | `/nl/airco-la-nucia/` | `/ru/konditsioner-la-nucia/` | `/no/aircondition-la-nucia/` |
| `orxeta` | `/es/aire-acondicionado-orxeta/` | `/en/air-conditioning-orxeta/` | `/de/klimaanlage-orxeta/` | `/nl/airco-orxeta/` | `/ru/konditsioner-orxeta/` | `/no/aircondition-orxeta/` |
| `polop` | `/es/aire-acondicionado-polop/` | `/en/air-conditioning-polop/` | `/de/klimaanlage-polop/` | `/nl/airco-polop/` | `/ru/konditsioner-polop/` | `/no/aircondition-polop/` |
| `relleu` | `/es/aire-acondicionado-relleu/` | `/en/air-conditioning-relleu/` | `/de/klimaanlage-relleu/` | `/nl/airco-relleu/` | `/ru/konditsioner-relleu/` | `/no/aircondition-relleu/` |
| `sella` | `/es/aire-acondicionado-sella/` | `/en/air-conditioning-sella/` | `/de/klimaanlage-sella/` | `/nl/airco-sella/` | `/ru/konditsioner-sella/` | `/no/aircondition-sella/` |
| `tarbena` | `/es/aire-acondicionado-tarbena/` | `/en/air-conditioning-tarbena/` | `/de/klimaanlage-tarbena/` | `/nl/airco-tarbena/` | `/ru/konditsioner-tarbena/` | `/no/aircondition-tarbena/` |
| `villajoyosa` | `/es/aire-acondicionado-villajoyosa/` | `/en/air-conditioning-villajoyosa/` | `/de/klimaanlage-villajoyosa/` | `/nl/airco-villajoyosa/` | `/ru/konditsioner-villajoyosa/` | `/no/aircondition-villajoyosa/` |

Decision: todas las landings vivas del sitemap se preservan en dev y en `public/sitemap.xml`; no se eliminan ni redirigen.
