# Rollback

## Dev

```bash
RELEASE_ID=20260525213000 bash ops/rollback-masqueclima-dev-release.sh
```

También acepta ruta absoluta:

```bash
bash ops/rollback-masqueclima-dev-release.sh /srv/apps/masqueclima-dev/releases/20260525213000
```

## Producción futuro

Protegido por confirmación explícita:

```bash
CONFIRM_PROD_ROLLBACK=yes RELEASE_ID=20260525213000 bash ops/rollback-masqueclima-prod-release.sh
```

Después de rollback ejecutar:

```bash
npm run seo:compare
curl -I https://masqueclima.es/es/
curl https://masqueclima.es/robots.txt
```
