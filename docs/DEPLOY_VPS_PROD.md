# Deploy VPS producción futuro

No ejecutar todavía.

Layout:

```text
/srv/apps/masqueclima
  docker-compose.yml
  current -> /srv/apps/masqueclima/releases/<timestamp>
  releases/
  shared/
    .env
    logs/
```

Puerto interno: `127.0.0.1:18111 -> 3000`.

El script `ops/deploy-masqueclima-prod-release.sh` está protegido:

```bash
CONFIRM_PROD_DEPLOY=yes bash ops/deploy-masqueclima-prod-release.sh
```

No usar hasta que se cumpla `docs/SEO_CUTOVER_CHECKLIST.md`.

Variables producción:

```bash
APP_ENV=production
NEXT_PUBLIC_SITE_URL=https://masqueclima.es
NEXT_PUBLIC_PRODUCTION_URL=https://masqueclima.es
```

En producción no debe existir `X-Robots-Tag: noindex` ni meta noindex.
