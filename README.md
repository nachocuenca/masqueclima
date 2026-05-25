# Masqueclima Next.js SEO-first

Rebuild de Masqueclima en Next.js App Router, TypeScript y Tailwind, preparado para staging en `https://dev.masqueclima.es` y futura migración controlada de `https://masqueclima.es`.

Producción actual no se toca desde este repo. Todo despliegue operativo debe validarse primero en `dev.masqueclima.es`.

## Local

```bash
npm install
cp .env.example .env
npm run dev
```

Abrir `http://localhost:3000/es/`.

Para probar la build standalone:

```bash
npm run build
npm run start
```

## Verificación

```bash
npm run lint
npm run build
npm run seo:audit -- http://localhost:3000
npm run seo:compare
```

En local y staging `APP_ENV` debe ser `staging`; esto fuerza `robots.txt` cerrado, meta noindex y cabecera `X-Robots-Tag`.

## Deploy dev VPS

```bash
cd /home/debian/repos/masqueclima
bash ops/deploy-masqueclima-dev-release.sh
```

Rollback:

```bash
RELEASE_ID=20260525213000 bash ops/rollback-masqueclima-dev-release.sh
```

## Documentación clave

- `docs/SEO_BASELINE_ACTUAL.md`
- `docs/URL_MAP_CURRENT.md`
- `docs/CANONICAL_HREFLANG_STRATEGY.md`
- `docs/MIGRATION_PLAN.md`
- `docs/SEO_RISKS.md`
- `docs/DEPLOY_VPS_DEV.md`
- `docs/SEO_CUTOVER_CHECKLIST.md`
- `docs/POST_DEPLOY_MONITORING.md`
