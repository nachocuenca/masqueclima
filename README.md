# +QUECLIMA / masqueclima

Web PHP multiidioma de +QUECLIMA.

> **PRODUCCIÓN — LEER ANTES DE TOCAR NADA**
>
> - Rama canónica de producción: **`production`**.
> - **`main` NO es la fuente de verdad de producción** y no debe desplegarse directamente.
> - Origen web público: VPS `51.254.128.162` (nginx + PHP-FPM).
> - Nicalia/cPanel (`cronos.dns-es.com`) se mantiene para infraestructura de correo/cPanel; **no es el origen web público**.
> - Runtime: `/srv/apps/masqueclima/current/public`.
> - Repo fuente en VPS: `/home/debian/repos/masqueclima`.

## Documentación operativa

- [`DEPLOY.md`](DEPLOY.md): procedimiento corto de despliegue.
- [`docs/PRODUCTION_INFRASTRUCTURE.md`](docs/PRODUCTION_INFRASTRUCTURE.md): mapa de infraestructura y responsabilidades.
- [`docs/DEPLOY.md`](docs/DEPLOY.md): procedimiento completo, validación y rollback.
- [`docs/RUNBOOK.md`](docs/RUNBOOK.md): operación diaria e incidencias.
- [`docs/BRANCHING.md`](docs/BRANCHING.md): política de ramas y fuente de verdad.
- [`docs/INCIDENT_2026-09-15_DEPLOY_ORIGIN.md`](docs/INCIDENT_2026-09-15_DEPLOY_ORIGIN.md): incidente que motivó esta normalización.

## Regla de oro

**Nunca copiar archivos directamente sobre `current` y nunca desplegar una rama distinta de `production`.** Los despliegues se hacen creando una nueva carpeta en `releases/`, enlazando `.env` y `storage` desde `shared/`, validando y cambiando el symlink `current` de forma atómica.

## Estado comercial temporal

La aplicación dispone del flag `ACCEPTING_NEW_WORK`:

- `false`: agenda cerrada; no se aceptan nuevos trabajos/presupuestos.
- `true`: captación abierta.

El valor de producción debe vivir en `/srv/apps/masqueclima/shared/.env`. Tras modificarlo, recargar PHP-FPM y ejecutar QA. Ver `docs/RUNBOOK.md`.
