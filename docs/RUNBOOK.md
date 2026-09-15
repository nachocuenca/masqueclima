# Runbook operativo de producción

## Referencias rápidas

- Web: `https://masqueclima.es`
- Health: `https://masqueclima.es/?__health=1`
- VPS: `51.254.128.162`
- SSH: `debian@51.254.128.162:22`
- App: `/srv/apps/masqueclima`
- Repo fuente: `/home/debian/repos/masqueclima`
- Rama de producción: `production`
- Nginx root: `/srv/apps/masqueclima/current/public`
- PHP-FPM: `php8.4-fpm`

## Antes de diagnosticar una incidencia

1. Confirmar que el dominio resuelve al VPS esperado:

```bash
getent ahostsv4 masqueclima.es
```

2. Confirmar origen HTTP:

```bash
curl -sSI https://masqueclima.es/ | grep -Ei 'HTTP/|server:'
```

La web pública debe salir del VPS/nginx. Si aparece LiteSpeed/Nicalia, investigar DNS/origen **antes** de tocar código.

3. Health:

```bash
curl -fsS https://masqueclima.es/?__health=1
```

## Despliegues

Usar exclusivamente el procedimiento de [`docs/DEPLOY.md`](DEPLOY.md). Nunca editar archivos dentro de `current` ni subir ZIPs directamente sobre producción.

## PHP-FPM / OPcache

Tras un cambio del symlink `current`, recargar siempre:

```bash
sudo systemctl reload php8.4-fpm
```

Motivo: el 2026-09-15, tras un switch correcto de release, producción siguió sirviendo código anterior hasta recargar PHP-FPM/OPcache.

Si la web sirve HTML viejo pero el filesystem y el symlink son correctos, comprobar este punto antes de modificar archivos.

## Agenda abierta/cerrada

Variable:

```text
ACCEPTING_NEW_WORK
```

Ubicación en producción:

```text
/srv/apps/masqueclima/shared/.env
```

Estados:

```text
ACCEPTING_NEW_WORK=false   # agenda cerrada
ACCEPTING_NEW_WORK=true    # agenda abierta
```

Si la variable falta, el código actual toma `false` como valor por defecto.

Después de cambiar el valor:

```bash
sudo systemctl reload php8.4-fpm
curl -fsS https://masqueclima.es/?__health=1
```

QA CLOSED:

- aviso de agenda cerrada en ES/EN/DE/NL/RU/NO;
- no CTA/modal/formulario funcional para nuevos presupuestos;
- POST directo bloqueado y redirigido con `sent=closed`;
- teléfono y WhatsApp disponibles para trabajos en curso.

QA OPEN:

- no banner de cierre;
- CTA/modal/formularios recuperados;
- POST normal vuelve a funcionar.

## Rollback

1. Identificar release anterior en `/srv/apps/masqueclima/releases/`.
2. Cambiar `current` a esa release mediante symlink atómico.
3. Recargar `php8.4-fpm`.
4. Validar health y rutas críticas.

No editar la release fallida en caliente.

## Logs

Antes de asumir una causa, identificar los logs configurados realmente en nginx/PHP-FPM. Comandos útiles:

```bash
sudo nginx -T 2>/dev/null | grep -E 'server_name|access_log|error_log|fastcgi_pass'
sudo systemctl status php8.4-fpm --no-pager
sudo journalctl -u php8.4-fpm -n 100 --no-pager
```

No compartir secretos de `.env`.

## Lentitud/intermitencia

Separar capas:

```bash
curl -4 -o /dev/null -s \
  -w 'DNS:%{time_namelookup} CONNECT:%{time_connect} TLS:%{time_appconnect} TTFB:%{time_starttransfer} TOTAL:%{time_total} HTTP:%{http_code}\n' \
  --max-time 30 https://masqueclima.es/?__health=1
```

Interpretación:

- DNS alto -> resolución.
- CONNECT alto -> red/origen.
- TLS alto -> handshake/certificado.
- TTFB alto con conexión rápida -> nginx/PHP/app.
- health rápido pero página lenta -> render/assets/terceros.

## Correo

Correo y web son infraestructuras separadas.

- Web -> VPS.
- Correo/cPanel -> Nicalia/cronos.

No cambiar el A web para resolver correo. Para incidencias de email auditar por separado MX, SPF, DKIM, DMARC y servidor SMTP/IMAP.

## Seguridad operativa

- No mostrar `.env` completo.
- No hacer `reset --hard` sobre runtime.
- No usar `rsync --delete` contra `current`.
- No borrar releases durante un despliegue.
- No tocar DNS en un deploy de aplicación.
- Todo hotfix parte de `production` y termina integrado en `production`.
