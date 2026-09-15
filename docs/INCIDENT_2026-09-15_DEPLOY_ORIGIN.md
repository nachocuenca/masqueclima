# Incidente 2026-09-15 — origen web y despliegue incoherente

## Resumen

El 2026-09-15 se intentó publicar el modo **Agenda temporalmente cerrada**. Inicialmente se asumió que la web pública vivía en Nicalia/cPanel porque allí estaban el dominio/correo y una copia de la aplicación.

El paquete se subió correctamente a Nicalia, pero la web pública no cambió. Además se observaron síntomas aparentemente contradictorios: CTA antiguo visible, modal que no funcionaba y estilos distintos.

## Síntomas

- `masqueclima.es` respondía de forma intermitente/lenta durante parte del diagnóstico, aunque posteriormente el healthcheck volvió a ser rápido.
- La web pública no mostraba el aviso de agenda cerrada.
- El HTML vivo conservaba `quoteModal` y formularios antiguos.
- CSS/JS públicos no coincidían con el commit que se acababa de preparar.

## Hallazgo 1 — dos orígenes distintos

DNS y pruebas forzadas mostraron:

```text
masqueclima.es      -> 51.254.128.162 -> nginx
cronos.dns-es.com   -> 92.60.32.27    -> LiteSpeed / Nicalia
```

Conclusión:

- La **web pública** se servía desde el VPS `51.254.128.162`.
- Nicalia/cronos contenía otra copia y seguía siendo relevante para correo/cPanel, pero no era el origen web.

Este fue el motivo por el que un despliegue correcto en Nicalia no produjo ningún cambio público.

## Hallazgo 2 — producción no correspondía a un único commit

El runtime real del VPS estaba en:

```text
/srv/apps/masqueclima/current -> /srv/apps/masqueclima/releases/20260607_211319
```

La comparación de hashes identificó archivos procedentes de varios commits históricos:

- `app/config.php` y `public/contact-submit.php`: línea de `2117f25`.
- `app/helpers.php` y `app/front_controller.php`: línea de `ac20bcd`.
- `public/assets/css/styles.css` y `public/assets/js/main.js`: línea de `8613916`.

Por tanto, la release viva era una mezcla histórica y no una reproducción exacta de un commit Git.

## Hallazgo 3 — `main` tampoco era la línea moderna de producción

El primer parche de agenda cerrada (`29df692`) había partido de `810d91a`, mientras que la rama moderna `fix/legacy-php-dev-stabilization` había continuado hasta `49819fc`.

Desplegar `29df692` íntegro habría sustituido código moderno por versiones antiguas y podía perder guías, reviews, CSS/JS, mailer y mejoras SEO posteriores.

## Resolución

1. Se tomó `49819fc` como base moderna.
2. Se creó `hotfix/closed-agenda-prod-2026-09-15`.
3. Se portó semánticamente la funcionalidad de agenda cerrada sin reemplazar archivos completos antiguos.
4. Commit resultante:

```text
b343950 Temporarily close new quote requests
```

5. Se creó backup:

```text
/srv/apps/masqueclima/backups/current_20260915_235049.tar.gz
```

6. Se creó release nueva:

```text
/srv/apps/masqueclima/releases/20260915_235049
```

7. Se cambió `current` a la nueva release.
8. Fue necesario recargar `php8.4-fpm` porque OPcache seguía sirviendo código de la release anterior tras el cambio de symlink.
9. QA final PASS en los seis idiomas, healthcheck, formularios bloqueados y Google Reviews preservado.

## Estado final verificado

- `?__health=1` -> `OK`.
- ES/EN/DE/NL/RU/NO -> aviso de agenda cerrada.
- `data-bs-target="#quoteModal"` -> 0 en HTML cerrado.
- formulario `/contact-submit.php` -> no se expone en HTML cerrado.
- POST directo -> redirección a `sent=closed`.
- Reviews -> presentes.
- Tiempos de health ~0.16–0.20 s.

## Acciones preventivas

- Rama canónica: `production`.
- `main` no se despliega.
- Producción se despliega solo mediante `releases/` + symlink `current`.
- Nunca subir ZIPs/código web a Nicalia esperando modificar la web pública.
- Separar explícitamente WEB (VPS) de CORREO/cPanel (Nicalia).
- Recargar PHP-FPM tras cada switch de release.
- Antes de un deploy, verificar DNS/origen, rama, commit y healthcheck.
- Toda documentación antigua de Nicalia para despliegue web se marca como histórica.
