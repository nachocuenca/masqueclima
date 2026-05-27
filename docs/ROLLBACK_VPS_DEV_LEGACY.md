# Rollback VPS dev legacy

No toca produccion. Solo cambia el symlink `current` de staging.

## Ver releases

```bash
ls -1 /srv/apps/masqueclima-legacy-dev/releases
readlink -f /srv/apps/masqueclima-legacy-dev/current
```

## Rollback

```bash
chmod +x ops/rollback-masqueclima-legacy-dev-release.sh
ops/rollback-masqueclima-legacy-dev-release.sh <timestamp-release>
```

El script valida `nginx -t`, recarga Nginx y comprueba `/es/`.

## Rollback manual

```bash
sudo ln -sfn /srv/apps/masqueclima-legacy-dev/releases/<timestamp> /srv/apps/masqueclima-legacy-dev/current
sudo nginx -t
sudo systemctl reload nginx
curl -ksSI https://dev.masqueclima.es/es/
```
