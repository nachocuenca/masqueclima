# Deploy VPS dev

Dominio: `https://dev.masqueclima.es`.

Layout:

```text
/home/debian/repos/masqueclima

/srv/apps/masqueclima-dev
  docker-compose.yml
  current -> /srv/apps/masqueclima-dev/releases/<timestamp>
  releases/
  shared/
    .env
    logs/
```

## Preparar `.env`

```bash
sudo mkdir -p /srv/apps/masqueclima-dev/shared/logs
sudo cp /home/debian/repos/masqueclima/.env.example /srv/apps/masqueclima-dev/shared/.env
sudo nano /srv/apps/masqueclima-dev/shared/.env
```

Mínimo dev:

```bash
APP_ENV=staging
NEXT_PUBLIC_SITE_URL=https://dev.masqueclima.es
NEXT_PUBLIC_PRODUCTION_URL=https://masqueclima.es
```

## Desplegar

```bash
cd /home/debian/repos/masqueclima
bash ops/deploy-masqueclima-dev-release.sh
```

El script valida `git`, `rsync`, `docker`, `curl`, `sudo`, crea release, actualiza `current`, reconstruye Docker y comprueba:

- `http://127.0.0.1:18110/api/health/`
- `https://dev.masqueclima.es/api/health/`
- `X-Robots-Tag: noindex`
- `robots.txt` con `Disallow: /`

## Nginx

Copiar `nginx/dev.masqueclima.es.conf` a `/etc/nginx/sites-available/`, enlazar a `sites-enabled`, validar y recargar:

```bash
sudo nginx -t
sudo systemctl reload nginx
```
