# Infraestructura de producción de +QUECLIMA

Última verificación: **2026-09-15**.

## Fuente de verdad

La web pública de `masqueclima.es` **no se sirve desde Nicalia**. El origen web real es el VPS:

- IP pública: `51.254.128.162`
- Host: `vps-0222829b`
- SSH operativo: `debian@51.254.128.162:22`
- Web server: nginx
- PHP-FPM: `/run/php/php8.4-fpm.sock`
- Virtual host nginx: `/etc/nginx/sites-enabled/masqueclima.es.conf`
- Config origen: `/etc/nginx/sites-available/masqueclima.es.conf`
- `server_name`: `masqueclima.es www.masqueclima.es`
- Document root: `/srv/apps/masqueclima/current/public`

## DNS y correo

Estado observado el 2026-09-15:

- `masqueclima.es` A -> `51.254.128.162`
- `www.masqueclima.es` -> CNAME de `masqueclima.es`
- No había AAAA publicado.
- `cronos.dns-es.com` resolvía a `92.60.32.27` y corresponde a la infraestructura Nicalia/cPanel.

**Separación de responsabilidades:**

- **WEB** -> VPS `51.254.128.162`.
- **CORREO / CPANEL** -> Nicalia/cronos.

No cambiar el registro A de la web para solucionar correo. El correo debe diagnosticarse mediante sus registros y servicios propios (MX, SPF, DKIM, DMARC y configuración del proveedor). Antes de cualquier cambio DNS de correo, inventariar los registros actuales.

## Estructura de runtime

```text
/srv/apps/masqueclima/
├── current -> releases/<release-activa>
├── releases/
├── shared/
│   ├── .env
│   └── storage/
└── backups/
```

El código desplegado **no es un checkout Git dentro de `current`**. Cada deploy crea una release inmutable y cambia el symlink `current`.

## Git

Repo GitHub:

```text
https://github.com/nachocuenca/masqueclima.git
```

Repo fuente en VPS:

```text
/home/debian/repos/masqueclima
```

Rama canónica de producción:

```text
production
```

Reglas:

- `production` es la única rama desplegable.
- `main` no representa la línea de producción y no debe desplegarse.
- Los hotfix deben partir de `production` y volver a integrarse/avanzar `production` antes del siguiente deploy.
- Nunca reconstruir producción copiando archivos sueltos de commits diferentes.

## Estado conocido tras el incidente de septiembre de 2026

Release activada:

```text
/srv/apps/masqueclima/releases/20260915_235049
```

Commit funcional desplegado:

```text
b343950 Temporarily close new quote requests
```

Backup previo:

```text
/srv/apps/masqueclima/backups/current_20260915_235049.tar.gz
```

Tras el cambio de symlink fue necesario:

```bash
sudo systemctl reload php8.4-fpm
```

porque PHP-FPM/OPcache seguía sirviendo código de la release anterior.

## Agenda cerrada

El estado comercial se controla mediante:

```text
ACCEPTING_NEW_WORK
```

El código toma `false` como valor por defecto si no existe la variable.

- `ACCEPTING_NEW_WORK=false`: agenda cerrada.
- `ACCEPTING_NEW_WORK=true`: captación abierta.

En producción, el valor debe gestionarse en:

```text
/srv/apps/masqueclima/shared/.env
```

No copiar `.env` dentro de Git ni mostrar sus demás secretos en logs o documentación.

## Comprobaciones mínimas de origen

```bash
curl -fsS https://masqueclima.es/?__health=1
curl -sSI https://masqueclima.es/es/ | head
```

Para verificar qué servidor responde:

```bash
curl -sSI https://masqueclima.es/ | grep -i '^server:'
```

La producción esperada responde desde nginx. Si se observa LiteSpeed/Nicalia, revisar DNS/origen antes de desplegar nada.
