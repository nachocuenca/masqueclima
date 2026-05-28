# TURNSTILE_RUNTIME_AUDIT.md

Auditoría del estado real de Cloudflare Turnstile en dev local.
**Fecha:** 2026-05-28 | **Rama:** fix/legacy-php-dev-stabilization

---

## 1. Estado general

Turnstile está **implementado en código pero sin activar** por falta de claves reales.

Veredicto: **"Listo para activar con claves"** — no está activado.

---

## 2. Variables de entorno

| Variable              | Dónde se lee                               | Valor por defecto |
|-----------------------|--------------------------------------------|-------------------|
| `TURNSTILE_ENABLED`   | `app/config.php` + `public/contact-submit.php` | `false`           |
| `TURNSTILE_SITE_KEY`  | `app/config.php`                           | `null`            |
| `TURNSTILE_SECRET_KEY`| `app/config.php` + `public/contact-submit.php` | `null`            |

Lectura: `filter_var(getenv('TURNSTILE_ENABLED') ?: 'false', FILTER_VALIDATE_BOOLEAN)`.

No hay ningún secret hardcodeado. ✅

---

## 3. Frontend (`app/front_controller.php`)

### Script Cloudflare (línea ~190)

```php
$turnstileEnabled = (bool) config('turnstile.enabled', false);
$turnstileSiteKey = (string) (config('turnstile.site_key') ?? '');
if ($turnstileEnabled && $turnstileSiteKey !== '') {
  // inyecta <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" ...>
}
```

- Solo carga si `TURNSTILE_ENABLED=true` **Y** hay site key. ✅
- Sin claves: ningún script externo cargado. ✅

### Widget en modal (línea ~441)

```php
if ($turnstileEnabled && $turnstileSiteKey !== '' && !str_contains($modal, 'cf-turnstile')) {
  // inyecta <div class="cf-turnstile" data-sitekey="..." data-theme="light" data-language="$lang">
}
```

- Localizado con `data-language="$lang"`. ✅
- No duplica si ya está presente. ✅
- Sin claves: widget no aparece. ✅

---

## 4. Backend (`public/contact-submit.php`)

```php
$turnstileEnabled = filter_var(getenv('TURNSTILE_ENABLED') ?: 'false', FILTER_VALIDATE_BOOLEAN);
$turnstileSecret  = (string) (getenv('TURNSTILE_SECRET_KEY') ?: '');

if ($turnstileEnabled) {
  if ($turnstileSecret === '') {
    error_log('Turnstile: TURNSTILE_ENABLED=true but TURNSTILE_SECRET_KEY is not set.');
    redirect_with_status($returnTo, '2');  // fail safe
  }
  $tsToken = trim((string) ($_POST['cf-turnstile-response'] ?? ''));
  if ($tsToken === '' || !verify_turnstile($turnstileSecret, $tsToken, $_SERVER['REMOTE_ADDR'] ?? '')) {
    redirect_with_status($returnTo, '2');
  }
}
```

### `verify_turnstile()` (línea ~111)

- Llama a `https://challenges.cloudflare.com/turnstile/v0/siteverify` via POST. ✅
- Envía `secret`, `response`, `remoteip`. ✅
- Timeout 5 segundos. ✅
- Si API no responde: `return false` (fail safe). ✅
- No expone secret en respuesta al usuario. ✅

---

## 5. Compatibilidad con CSRF y honeypot

| Mecanismo   | Estado |
|-------------|--------|
| CSRF token  | ✅ Activo siempre (antes de Turnstile) |
| Honeypot `company` | ✅ Activo siempre (primera verificación) |
| Turnstile   | Se ejecuta DESPUÉS de CSRF+honeypot |

Orden de verificación en `contact-submit.php`:
1. Honeypot `company` → reject silencioso (send=1 fake)
2. CSRF check → reject 400
3. Turnstile (si enabled) → reject send=2
4. Validación de campos → reject send=0
5. Procesamiento + envío

---

## 6. Pruebas realizadas en dev local

### Turnstile disabled (estado por defecto sin env vars)

| Test | Resultado |
|------|-----------|
| `/es/` carga sin errores | ✅ |
| Formulario renderiza | ✅ |
| No aparece widget Cloudflare | ✅ |
| No carga script `api.js` | ✅ |
| CSRF presente en form | ✅ |
| Honeypot presente en form | ✅ |

### Turnstile enabled sin secret (simulado)

> ⚠️ **Limitación**: el servidor PHP dev fue iniciado como proceso separado, por lo que `$env:TURNSTILE_ENABLED` del shell actual NO propaga. Para pruebas runtime con env, reiniciar servidor con variables de entorno.

**Comportamiento esperado por código** (verificado por lectura estática):
- `contact-submit.php` lee `getenv('TURNSTILE_ENABLED')` → `true`
- `TURNSTILE_SECRET_KEY` no existe → `error_log(...)` + `redirect_with_status($returnTo, '2')`
- Formulario NO procesa — fail safe. ✅

---

## 7. Para activar en dev VPS

Editar el archivo shared `.env` del deploy:

```
/srv/apps/masqueclima-legacy-dev/shared/.env
```

Añadir:

```bash
TURNSTILE_ENABLED=true
TURNSTILE_SITE_KEY=0x...  # clave pública, obtenida del dashboard Cloudflare
TURNSTILE_SECRET_KEY=0x...  # clave secreta, NUNCA al repo
```

- La **site key** puede ser pública (va en el HTML).
- La **secret key** NUNCA se commitea al repo.
- Tras editar: `systemctl reload php-fpm` o redeploy según el flujo.
- Hacer prueba real de envío con token válido del widget.

> Para testing local sin VPS: reiniciar el servidor con variables:
> ```powershell
> $env:TURNSTILE_ENABLED="true"; $env:TURNSTILE_SITE_KEY="0xFAKE"; php -S localhost:8787 -t public public/index.php
> ```

---

## 8. Pendiente para activación real

- [ ] Crear cuenta Cloudflare Turnstile, obtener claves reales para dominio `masqueclima.es`
- [ ] Añadir al `.env` del VPS (no al repo)
- [ ] Redeploy / reload
- [ ] Prueba de envío real (token válido + token inválido + sin token)
- [ ] Confirmar que `sent=1` aparece con token válido
- [ ] Confirmar que `sent=2` aparece con token inválido/ausente

---

## 9. Veredicto de seguridad

| Aspecto | Estado |
|---------|--------|
| Sin secrets hardcodeados | ✅ |
| Fail safe sin secret | ✅ |
| No expone detalles técnicos al usuario | ✅ |
| CSRF sigue activo | ✅ |
| Honeypot sigue activo | ✅ |
| Sin romper formulario disabled | ✅ |
| Compatibilidad multilang widget | ✅ |
