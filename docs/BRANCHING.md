# Política de ramas

## Rama canónica

La fuente de verdad de producción es:

```text
production
```

**No desplegar `main`.** La rama `main` pertenece a una línea divergente que no representa el runtime actual del VPS.

## Flujo normal

Para cambios normales:

```text
production
  └── feature/... o fix/...
        └── revisión / QA
              └── integrar de nuevo en production
                    └── deploy por release
```

Para hotfix:

```text
production
  └── hotfix/...
        └── QA CLOSED/OPEN si aplica
              └── integrar/avanzar production
                    └── nueva release atómica
```

## Reglas

1. Toda rama destinada a producción parte de `production`.
2. Antes de desplegar, el commit debe pertenecer a la historia de `production`.
3. No portar archivos completos desde ramas antiguas si existen commits posteriores: integrar semánticamente el cambio sobre la rama moderna.
4. No mezclar archivos procedentes de varios commits en una release.
5. El VPS despliega código desde `/home/debian/repos/masqueclima` a `/srv/apps/masqueclima/releases/<timestamp>`; `current` nunca es un repo Git editable.
6. Un hotfix desplegado debe quedar incorporado en `production` antes de empezar el siguiente trabajo.
7. Las ramas históricas `fix/legacy-php-dev-stabilization` y `hotfix/closed-agenda-prod-2026-09-15` pueden conservarse como trazabilidad, pero no sustituyen a `production`.

## Por qué existe esta política

El 2026-09-15 se comprobó que:

- `main` había divergido de la línea moderna que alimentaba producción;
- producción contenía archivos correspondientes a distintos commits históricos;
- un parche creado desde una base antigua no podía desplegarse íntegramente sin riesgo de perder mejoras posteriores.

La solución fue reconstruir el hotfix sobre la rama moderna y desplegar una release atómica. Desde ese momento, `production` es la única referencia desplegable.

## Comprobación antes de cualquier deploy

```bash
cd /home/debian/repos/masqueclima
git fetch origin --prune
git switch production
git pull --ff-only origin production
git status --short
git log -1 --oneline
```

Si hay cambios locales o la rama no es `production`, detener el despliegue.
