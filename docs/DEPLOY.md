# Despliegue de producción — VPS

Documento autoritativo de despliegue. Sustituye a los procedimientos antiguos basados en Nicalia/cPanel para la **web**.

## 1. Entorno

- Dominio: `https://masqueclima.es`
- VPS: `51.254.128.162`
- Usuario operativo: `debian`
- Nginx root: `/srv/apps/masqueclima/current/public`
- Repo fuente: `/home/debian/repos/masqueclima`
- Rama desplegable: `production`
- Runtime: `/srv/apps/masqueclima`
- Shared env: `/srv/apps/masqueclima/shared/.env`
- Shared storage: `/srv/apps/masqueclima/shared/storage`
- PHP-FPM: `php8.4-fpm`

**Nicalia/cronos no es el origen web público.** Se conserva para correo/cPanel.

## 2. Principios obligatorios

1. No desplegar `main`.
2. No copiar ZIPs ni archivos sueltos sobre `current`.
3. No convertir `current` en checkout Git.
4. Cada deploy crea una release nueva e inmutable.
5. `.env` y `storage` siempre proceden de `shared/`.
6. No borrar la release anterior hasta finalizar QA.
7. Si falla el QA, rollback de symlink; no reparar en caliente.
8. No tocar DNS, nginx o correo durante un deploy de aplicación salvo incidencia independiente y documentada.

## 3. Preflight Git

```bash
cd /home/debian/repos/masqueclima
git fetch origin --prune
git switch production
git pull --ff-only origin production
git status --short
git log -1 --oneline
```

El working tree debe quedar limpio. Si no está limpio, **detener el deploy**.

## 4. Backup

Antes del switch, conservar como mínimo la release activa. Para cambios de riesgo o de infraestructura, crear además backup:

```bash
APP=/srv/apps/masqueclima
STAMP=$(date +%Y%m%d_%H%M%S)
mkdir -p "$APP/backups"
tar -czf "$APP/backups/current_$STAMP.tar.gz" -C "$APP" current shared
ls -lh "$APP/backups/current_$STAMP.tar.gz"
tar -tzf "$APP/backups/current_$STAMP.tar.gz" >/dev/null
```

No almacenar credenciales del `.env` en documentación o salida compartida.

## 5. Crear release

```bash
APP=/srv/apps/masqueclima
REPO=/home/debian/repos/masqueclima
STAMP=$(date +%Y%m%d_%H%M%S)
REL="$APP/releases/$STAMP"

mkdir -p "$REL"
rsync -a --delete \
  --exclude='.git/' \
  --exclude='.env' \
  --exclude='storage/' \
  "$REPO/" "$REL/"

ln -s "$APP/shared/.env" "$REL/.env"
ln -s "$APP/shared/storage" "$REL/storage"
```

Comprobar los symlinks:

```bash
readlink -f "$REL/.env"
readlink -f "$REL/storage"
```

## 6. QA antes del switch

Lint PHP:

```bash
cd "$REL"
find app views public public_html -type f -name '*.php' -print0 2>/dev/null \
  | xargs -0 -n1 php -l
```

Comprobar archivos y permisos críticos. Si el cambio afecta configuración comercial, validar ambos estados cuando sea aplicable.

Para agenda cerrada:

- CLOSED: banner visible, sin CTA/formularios de nueva captación, POST bloqueado.
- OPEN (`ACCEPTING_NEW_WORK=true`): comportamiento original restaurado.

No cambiar `current` si el preflight falla.

## 7. Switch atómico

Guardar la release previa:

```bash
APP=/srv/apps/masqueclima
PREV=$(readlink -f "$APP/current")
echo "$PREV"
```

Cambiar:

```bash
ln -sfn "$REL" "$APP/current.new"
mv -Tf "$APP/current.new" "$APP/current"
```

Después **recargar PHP-FPM**:

```bash
sudo systemctl reload php8.4-fpm
```

Esto es obligatorio tras cambios de release: el 2026-09-15 se verificó que OPcache/PHP-FPM podía seguir sirviendo código del symlink anterior hasta el reload.

No es necesario recargar nginx si su configuración no ha cambiado.

## 8. QA post-deploy

Health:

```bash
curl -fsS https://masqueclima.es/?__health=1
```

Debe devolver `OK`.

Rutas mínimas:

```text
/es/
/en/
/de/
/nl/
/ru/
/no/
/es/aire-acondicionado-benidorm/
/es/servicios/instalacion-aire-acondicionado/
```

Verificar:

- HTTP 200.
- CSS/JS cargan 200.
- Tipografía y navbar correctas.
- Google Reviews y guías siguen presentes.
- Canonical/hreflang/JSON-LD sin regresiones.
- Sin warnings/fatals PHP.
- `?__health=1` rápido.

Si agenda está cerrada, además:

- aparece el aviso localizado;
- `data-bs-target="#quoteModal"` no es accionable/presente en el HTML final;
- no hay formulario funcional de nuevas solicitudes;
- POST directo a `/contact-submit.php` redirige con `sent=closed`;
- teléfono y WhatsApp quedan para trabajos en curso.

## 9. Rollback

Si falla cualquier comprobación crítica:

```bash
APP=/srv/apps/masqueclima
# PREV debe contener la ruta absoluta guardada antes del switch
ln -sfn "$PREV" "$APP/current.new"
mv -Tf "$APP/current.new" "$APP/current"
sudo systemctl reload php8.4-fpm
curl -fsS https://masqueclima.es/?__health=1
```

No borrar la release fallida hasta analizar la causa.

## 10. Limpieza de releases

La limpieza debe hacerse en una tarea separada del deploy y nunca antes de confirmar estabilidad. Conservar como mínimo la release activa, la anterior y cualquier release necesaria para rollback/auditoría.

## 11. Correo

El correo está separado de la web. Nicalia/cronos sigue formando parte de la infraestructura de correo. No cambiar el A de `masqueclima.es` para solucionar incidencias de email: revisar MX/SPF/DKIM/DMARC y configuración de correo por separado.
