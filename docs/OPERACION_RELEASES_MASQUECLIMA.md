# Operación por releases

Patrón operativo:

- repo fuente: `/home/debian/repos/masqueclima`;
- app dev: `/srv/apps/masqueclima-dev`;
- app prod futura: `/srv/apps/masqueclima`;
- release inmutable por timestamp;
- `current` apunta atómicamente a la release activa;
- `shared/.env` y `shared/logs` sobreviven a releases.

Exclusiones de rsync:

- `.git`
- `node_modules`
- `.next`
- `.env`
- `.env.*`
- `logs`
- `storage`

Validaciones obligatorias dev:

- healthcheck local;
- healthcheck HTTPS;
- robots cerrado;
- `X-Robots-Tag` noindex.

Validaciones obligatorias producción futura:

- healthcheck local;
- healthcheck HTTPS;
- `/` redirige a `/es/`;
- `/es/` 200;
- no existe noindex;
- sitemap contiene canónicas reales.
