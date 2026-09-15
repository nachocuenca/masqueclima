# Deploy de +QUECLIMA

**Producción web:** VPS `51.254.128.162`  
**Rama fuente:** `production`  
**Repo VPS:** `/home/debian/repos/masqueclima`  
**Runtime:** `/srv/apps/masqueclima`

> Nicalia/cPanel no es el origen web público. No desplegar la web allí.

## Flujo obligatorio

1. Actualizar el repo fuente del VPS y comprobar que está limpio.
2. Desplegar **solo** desde `production`.
3. Crear una nueva carpeta `releases/<timestamp>`; nunca sobrescribir `current`.
4. Copiar el código a la release excluyendo `.git`, `.env` y `storage` runtime.
5. Enlazar:
   - `.env -> /srv/apps/masqueclima/shared/.env`
   - `storage -> /srv/apps/masqueclima/shared/storage`
6. Ejecutar lint/QA antes del switch.
7. Cambiar `current` de forma atómica a la nueva release.
8. Recargar `php8.4-fpm` para evitar que OPcache siga sirviendo código de la release anterior.
9. Verificar `https://masqueclima.es/?__health=1` y rutas críticas.
10. Conservar la release anterior para rollback inmediato.

## Comandos base

```bash
cd /home/debian/repos/masqueclima
git fetch origin --prune
git switch production
git pull --ff-only origin production
git status

APP=/srv/apps/masqueclima
STAMP=$(date +%Y%m%d_%H%M%S)
REL="$APP/releases/$STAMP"
mkdir -p "$REL"

rsync -a --delete \
  --exclude='.git/' \
  --exclude='.env' \
  --exclude='storage/' \
  /home/debian/repos/masqueclima/ "$REL/"

ln -s "$APP/shared/.env" "$REL/.env"
ln -s "$APP/shared/storage" "$REL/storage"
```

Validar la release antes del switch. Después:

```bash
ln -sfn "$REL" "$APP/current.new"
mv -Tf "$APP/current.new" "$APP/current"
sudo systemctl reload php8.4-fpm
curl -fsS https://masqueclima.es/?__health=1
```

## Rollback

Si el QA falla, volver el symlink `current` a la release anterior y recargar PHP-FPM. **No reparar en caliente dentro de `current`.**

Procedimiento completo: [`docs/DEPLOY.md`](docs/DEPLOY.md).
