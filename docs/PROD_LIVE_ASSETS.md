# Assets y dependencias de produccion

## Assets criticos preservados en repo

| Ruta | Existe local | Bytes |
| --- | --- | ---: |
| `/assets/css/styles.css` | si | `9232` |
| `/assets/js/main.js` | si | `3585` |
| `/assets/img/hero.mp4` | si | `1996208` |
| `/assets/img/hero1.jpg` | si | `23551` |
| `/assets/img/hero1.webp` | si | `33586` |
| `/assets/img/og.jpg` | si | `21715` |
| `/assets/img/masqueclimalogo_.png` | si | `22639` |
| `/assets/img/flags/no.svg` | si | `548` |
| `/assets/img/Daikin-Logo.png` | si | `3495` |
| `/assets/img/Mitsubishi-Electric-Logo.png` | si | `4430` |
| `/assets/img/Logo-Fujitsu.png` | si | `3760` |
| `/assets/img/Logo Panasonic.png` | si | `3969` |
| `/assets/img/Haier-Logo.wine.png` | si | `3402` |
| `/assets/img/LG-logo.png` | si | `4715` |
| `/assets/img/gree-logo.png` | si | `3501` |
| `/assets/img/giatsu-01.png` | si | `2938` |
| `/assets/img/especialista-limpia-y-repara-el-aire-acondicionado-de-pared.jpg` | si | `196257` |
| `/assets/img/hero-benidorm.webp` | si | `195152` |
| `/assets/img/hero-guadalest.webp` | si | `238877` |

## Dependencias externas observadas

- WhatsApp: `https://wa.me/34613026600`.
- Google Maps/cookies: referencias en paginas de produccion.
- Elfsight/resenas: referencias observadas en HTML/cookies/documentacion legacy.
- Bootstrap/JS del tema: preservado desde la captura HTML y assets locales.

Regla operativa: no eliminar assets legacy hasta validar que no existen referencias en snapshots ni en produccion viva.
