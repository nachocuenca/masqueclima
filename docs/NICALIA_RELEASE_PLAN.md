# NICALIA RELEASE PLAN
**Branch fuente:** `fix/legacy-php-dev-stabilization`  
**HEAD previsto:** `a6210fe fix: add cookie policy page` + sitemap update commit  
**Fecha plan:** 2026-05-28  
**Estado:** PREPARADO — NO ejecutar hasta validación visual completa en dev.

---

## ⚠️ ADVERTENCIAS CRÍTICAS

1. **NO tocar producción sin backup completo primero.**
2. **NO tocar DNS** salvo si es absolutamente necesario y consensuado.
3. **NO mezclar con WordPress ni Kit Digital** en el mismo despliegue.
4. **NO subir si el dev local no está validado visualmente** (formulario, modal, hero images).
5. **NO modificar `.htaccess` de Nicalia** sin revisión previa del .htaccess del repo.
6. **Limpiar caché LiteSpeed** obligatoriamente tras subida.

---

## Objetivo del release

Desplegar la versión estabilizada de la web legacy PHP multiidioma de Masqueclima en el servidor de Nicalia (`public_html`), incluyendo:

- Multiidioma completo (es/en/de/nl/ru/no)
- Hubs de servicios, zonas y guías en 6 idiomas
- 30 páginas de servicios multiidioma
- 120 landings locales (20 localidades × 6 idiomas) con heroes locales
- Selector de idioma contextual
- CTA final localizado
- Footer limpio
- Modal de presupuesto localizado
- Página /politica-de-cookies
- Sitemap actualizado (174 URLs)
- Robots.txt limpio

---

## Alcance de cambios vs. producción actual

Los siguientes archivos/directorios han cambiado respecto al estado anterior en producción:

```
app/front_controller.php   (cambios mayores: hubs, servicios, heroes, cookie policy)
app/config.php             (sin cambios relevantes)
app/helpers.php            (sin cambios relevantes)
app/content/hubs/          (nuevos: de.php, en.php, nl.php, no.php, ru.php)
app/content/services/      (nuevos: es.php, en.php, de.php, nl.php, no.php, ru.php)
app/snapshots/             (snapshots completos 6 idiomas × 20+1 localidades)
app/translations/          (de.php, en.php, nl.php, no.php, ru.php, es.php)
views/                     (layout.php y vistas actualizadas)
public/sitemap.xml         (174 URLs, era 126)
public/robots.txt          (sin cambios)
public/index.php           (sin cambios relevantes)
public/assets/img/heroes/  (heroes locales + heroes hubs)
```

---

## Backup previo en Nicalia (OBLIGATORIO)

Antes de tocar nada en Nicalia, ejecutar **en el servidor**:

```bash
# 1. Backup completo de public_html
cd ~
tar -czf backup_public_html_$(date +%Y%m%d_%H%M%S).tar.gz public_html/

# 2. Verificar que el backup existe y tiene tamaño razonable
ls -lh backup_public_html_*.tar.gz

# 3. Backup individual de archivos críticos por si acaso
cp public_html/.htaccess ~/backup_.htaccess_$(date +%Y%m%d)
cp public_html/robots.txt ~/backup_robots_$(date +%Y%m%d).txt
cp public_html/sitemap.xml ~/backup_sitemap_$(date +%Y%m%d).xml
```

---

## Verificar versión PHP compatible

```bash
php --version
# Requiere PHP 8.1+ (declaraciones strict, match, str_ends_with, etc.)
# Si PHP < 8.1, NO subir — reportar a Nicalia/hosting para upgrade.
```

---

## Orden de subida

### Paso 1 — Subir archivos de aplicación (sin tocar public_html raíz)

```bash
# Desde local, vía rsync o FTP (usar rsync si hay acceso SSH):
rsync -avz --delete \
  app/ \
  views/ \
  vendor/ \
  user@nicalia-server:~/public_html/app/

rsync -avz --delete \
  app/snapshots/ \
  user@nicalia-server:~/public_html/app/snapshots/
```

### Paso 2 — Subir assets

```bash
rsync -avz \
  public/assets/img/heroes/ \
  user@nicalia-server:~/public_html/public/assets/img/heroes/

rsync -avz \
  public/assets/css/ \
  user@nicalia-server:~/public_html/public/assets/css/

rsync -avz \
  public/assets/js/ \
  user@nicalia-server:~/public_html/public/assets/js/
```

### Paso 3 — Subir archivos PHP públicos

```bash
rsync -avz \
  public/index.php \
  public/sitemap.xml \
  public/robots.txt \
  user@nicalia-server:~/public_html/public/
```

### Paso 4 — Subir/revisar .htaccess

**ATENCIÓN:** Revisar el `.htaccess` del repo antes de sobreescribir el de Nicalia.
Si Nicalia tiene reglas específicas (LiteSpeed, caché, seguridad), no sobreescribir a ciegas.

```bash
# Primero, comparar:
diff ~/backup_.htaccess_* public_html/.htaccess

# Solo si es seguro:
rsync -avz public/.htaccess user@nicalia-server:~/public_html/public/
```

### Paso 5 — Establecer permisos correctos

```bash
find ~/public_html -type f -exec chmod 644 {} \;
find ~/public_html -type d -exec chmod 755 {} \;
# Si hay scripts ejecutables:
chmod 755 ~/public_html/public/contact-submit.php
```

### Paso 6 — Variables de entorno (si aplica)

- Verificar que `APP_ENV=production` esté configurado en el servidor (o que no esté, porque el default es `production`).
- Verificar que `SMTP_HOST`, `SMTP_USER`, `SMTP_PASS` estén configurados en el servidor.
- **NO hardcodear credenciales**. Usar el panel de Nicalia o `.env` si lo soporta.

### Paso 7 — Limpiar caché LiteSpeed

```bash
# Vía panel cPanel/Nicalia si disponible:
# LiteSpeed Cache → Flush All

# O vía comando si hay acceso:
# rm -rf /tmp/lscache/*
```

---

## Validaciones post-subida

Ejecutar todas estas comprobaciones inmediatamente tras subir:

```bash
BASE="https://masqueclima.es"

# Redirect raíz
curl -sI "$BASE/" | grep -E "HTTP|Location"
# Esperado: HTTP 301, Location: .../es/

# Homes
for lang in es en de nl ru no; do
  code=$(curl -sI "$BASE/$lang/" | awk '/^HTTP/{print $2}')
  echo "$lang/ → $code"
done

# Hubs (muestra)
for url in es/servicios/ en/services/ de/dienstleistungen/ nl/diensten/ ru/uslugi/ no/tjenester/; do
  code=$(curl -sI "$BASE/$url" | awk '/^HTTP/{print $2}')
  echo "$url → $code"
done

# Servicios (muestra)
for url in es/servicios/instalacion-aire-acondicionado/ en/services/air-conditioning-installation/ de/dienstleistungen/klimaanlage-installation/; do
  code=$(curl -sI "$BASE/$url" | awk '/^HTTP/{print $2}')
  echo "$url → $code"
done

# Landings (muestra)
for url in es/aire-acondicionado-benidorm/ en/air-conditioning-benidorm/ de/klimaanlage-benidorm/ nl/airco-benidorm/ ru/konditsioner-benidorm/ no/aircondition-benidorm/; do
  code=$(curl -sI "$BASE/$url" | awk '/^HTTP/{print $2}')
  echo "$url → $code"
done

# Legal — páginas legales multiidioma
for url in es/aviso-legal/ es/politica-de-privacidad/ es/politica-de-cookies/ en/legal-notice/ en/privacy-policy/ en/cookie-policy/ de/impressum/ de/datenschutzerklaerung/ de/cookie-richtlinie/ nl/juridische-mededeling/ nl/privacybeleid/ nl/cookiebeleid/ ru/pravovoe-uvedomlenie/ ru/politika-konfidentsialnosti/ ru/cookie-policy/ no/juridisk-varsel/ no/personvernerklaering/ no/cookie-policy/; do
  code=$(curl -sI "$BASE/$url" | awk '/^HTTP/{print $2}')
  echo "$url → $code"
done

# Compatibilidad legacy
curl -sI "$BASE/politica-de-cookies" | head -2

# Sitemap y robots
curl -sI "$BASE/sitemap.xml" | head -2
curl -sI "$BASE/robots.txt" | head -2

# Formulario — solo verificar que no da 500
curl -sI "$BASE/contact-submit.php" | head -2
```

### Checklist de validación manual (navegador)

- [ ] `/` → redirige a `/es/` sin error
- [ ] `/es/` carga home ES con hero, navbar, modal, footer
- [ ] `/en/` carga home EN (título EN, idioma correcto)
- [ ] `/de/` carga home DE
- [ ] Selector de idioma funciona en cada idioma
- [ ] `/es/servicios/` hub carga correctamente con listado de servicios
- [ ] `/es/servicios/instalacion-aire-acondicionado/` carga con contenido correcto
- [ ] `/es/aire-acondicionado-benidorm/` carga con hero local (no fallback)
- [ ] `/politica-de-cookies` → 301 → `/es/politica-de-cookies/` sin bucle
- [ ] `/es/aviso-legal/` carga con `DANIEL CUENCA MOYA` visible y email `administracion@masqueclima.es`
- [ ] `/en/legal-notice/` ídem en inglés
- [ ] Modal de presupuesto abre y cierra en cada idioma
- [ ] Formulario de contacto no rompe (submit va a `/contact-submit.php`)
- [ ] `/robots.txt` accesible
- [ ] `/sitemap.xml` accesible y válido
- [ ] Canonical en home: `https://masqueclima.es/es/`
- [ ] Hreflang presente y correcto en home
- [ ] Sin errores PHP visibles (no `Fatal error`, no `Warning:` en HTML)
- [ ] Assets CSS/JS/imágenes cargan (inspeccionar red)
- [ ] Sin Forbidden 403
- [ ] Sin `noindex` accidental en producción

---

## Checklist SEO post-subida (24–48h tras deploy)

- [ ] Google Search Console: solicitar re-rastreo de sitemap
- [ ] GSC: verificar que `/sitemap.xml` es accesible y se procesa
- [ ] GSC: comprobar errores de cobertura nuevos
- [ ] GSC: confirmar que URLs de homes/hubs/servicios empiezan a aparecer
- [ ] Verificar hreflang con herramienta externa (hreflang.org o similar)
- [ ] Verificar que reformas no aparecen en GSC como indexadas (si ya estaban, pueden tardar en desaparecer)

---

## Rollback

Si algo falla tras subir, ejecutar **inmediatamente**:

```bash
# 1. Restaurar backup de public_html
cd ~
tar -xzf backup_public_html_YYYYMMDD_HHMMSS.tar.gz

# 2. Restaurar .htaccess si fue modificado
cp ~/backup_.htaccess_YYYYMMDD public_html/.htaccess

# 3. Limpiar caché LiteSpeed
# (vía panel o rm -rf /tmp/lscache/*)

# 4. Validar que la web vuelve a estar accesible
curl -sI https://masqueclima.es/ | head -3
curl -sI https://masqueclima.es/es/ | head -3
```

**Criterio de rollback:** Si cualquiera de estos falla tras deploy:
- `/es/` devuelve 500 o 404
- Formulario devuelve error visible
- Más del 20% de URLs devuelven 404 o 500
- PHP fatal en cualquier página pública

---

## Notas adicionales

- **Nicalia y WordPress/Kit Digital:** si coexisten en el mismo hosting, verificar que los `VirtualHost` y `DocumentRoot` están bien separados antes de subir.
- **LiteSpeed caché:** puede servir versiones cacheadas de páginas antiguas hasta varios minutos tras el deploy. Limpiar siempre.
- **Snapshots:** los archivos HTML en `app/snapshots/` son la fuente de verdad de las landings locales. No borrarlos ni modificarlos sin control de versiones.
- **PHP y `strict_types`:** el código usa `declare(strict_types=1)`. PHP 8.0+ requerido, 8.1+ recomendado.
