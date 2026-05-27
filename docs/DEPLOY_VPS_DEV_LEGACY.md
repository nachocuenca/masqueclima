# Deploy VPS dev legacy

Objetivo: servir la web PHP legacy en `https://dev.masqueclima.es` sin tocar Nicalia.

## Estructura

```text
/home/debian/repos/masqueclima
/srv/apps/masqueclima-legacy-dev
  current -> releases/<timestamp>
  releases/
  shared/
    .env
    logs/
```

## Preparacion del VPS

```bash
sudo mkdir -p /srv/apps/masqueclima-legacy-dev/{releases,shared/logs}
sudo chown -R debian:www-data /srv/apps/masqueclima-legacy-dev
cd /home/debian/repos/masqueclima
git fetch origin
git checkout fix/legacy-php-dev-stabilization
```

Crear o revisar `/srv/apps/masqueclima-legacy-dev/shared/.env`:

```env
APP_ENV=staging
CONTACT_LOG_DIR=/srv/apps/masqueclima-legacy-dev/shared/logs
SMTP_HOST=masqueclima.es
```

Si se configuran credenciales SMTP, usar `SMTP_HOST=masqueclima.es`; no usar `mail.masqueclima.es`.

Instalar la conf Nginx:

```bash
sudo cp nginx/dev.masqueclima.es.legacy.conf /etc/nginx/sites-available/dev.masqueclima.es.legacy.conf
sudo ln -sfn /etc/nginx/sites-available/dev.masqueclima.es.legacy.conf /etc/nginx/sites-enabled/dev.masqueclima.es.legacy.conf
sudo nginx -t
sudo systemctl reload nginx
```

Si el socket PHP-FPM no es `/run/php/php8.4-fpm.sock`, ajustar `fastcgi_pass` antes de recargar.

## Deploy

```bash
chmod +x ops/deploy-masqueclima-legacy-dev-release.sh
BRANCH=fix/legacy-php-dev-stabilization ops/deploy-masqueclima-legacy-dev-release.sh
```

Si la rama aun no esta publicada y el repo del VPS ya contiene el working tree copiado manualmente:

```bash
REPO_DIR=/home/debian/repos/masqueclima-legacy-dev SKIP_GIT=1 ops/deploy-masqueclima-legacy-dev-release.sh
```

Nota del primer despliegue: como `/home/debian/repos/masqueclima` tenia WIP Next remoto, se uso una copia limpia legacy en `/home/debian/repos/masqueclima-legacy-dev` para no mezclar ramas.

## Validacion rapida

```bash
curl -ksSI --max-redirs 0 https://dev.masqueclima.es/
curl -ksSI https://dev.masqueclima.es/es/ | grep -i X-Robots-Tag
curl -ksS https://dev.masqueclima.es/robots.txt
curl -ksS https://dev.masqueclima.es/sitemap.xml | grep -c '<loc>'
```
