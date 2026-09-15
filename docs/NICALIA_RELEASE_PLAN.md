# NICALIA RELEASE PLAN — HISTÓRICO

> **NO USAR PARA DESPLEGAR LA WEB DE PRODUCCIÓN.**
>
> Este documento describía el plan previsto en mayo de 2026, cuando se asumía que `masqueclima.es` se serviría desde Nicalia/cPanel. El 2026-09-15 se verificó que el origen web público real es el VPS `51.254.128.162` con nginx y PHP-FPM.

## Situación actual

- WEB pública -> VPS `51.254.128.162`.
- Runtime -> `/srv/apps/masqueclima`.
- Nginx root -> `/srv/apps/masqueclima/current/public`.
- Repo fuente VPS -> `/home/debian/repos/masqueclima`.
- Rama desplegable -> `production`.
- CORREO/cPanel -> Nicalia/cronos.

Nicalia sigue siendo relevante para la infraestructura de correo y administración asociada, pero **subir código web a Nicalia no modifica la web pública mientras el registro A siga apuntando al VPS**.

## Documentos vigentes

Usar exclusivamente:

- [`../DEPLOY.md`](../DEPLOY.md)
- [`PRODUCTION_INFRASTRUCTURE.md`](PRODUCTION_INFRASTRUCTURE.md)
- [`DEPLOY.md`](DEPLOY.md)
- [`RUNBOOK.md`](RUNBOOK.md)
- [`BRANCHING.md`](BRANCHING.md)
- [`INCIDENT_2026-09-15_DEPLOY_ORIGIN.md`](INCIDENT_2026-09-15_DEPLOY_ORIGIN.md)

## Nota de correo

No confundir origen web y correo. No repuntar el A de `masqueclima.es` a Nicalia para resolver email. Las incidencias de correo deben tratarse mediante MX/SPF/DKIM/DMARC y configuración del servicio de correo.

El contenido original de este plan permanece disponible en el historial Git anterior a esta actualización.
