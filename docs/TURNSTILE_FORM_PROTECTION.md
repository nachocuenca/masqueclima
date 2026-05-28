# Cloudflare Turnstile — Form Protection

**Status:** Implemented (server + client), **disabled by default**  
**Date:** 2026-05-28  
**Branch:** `fix/legacy-php-dev-stabilization`

---

## Overview

Cloudflare Turnstile is integrated on the quote modal form as an optional anti-spam layer. It layers on top of the existing CSRF token + honeypot checks.

When `TURNSTILE_ENABLED=false` (default), Turnstile is completely skipped — no script loaded, no widget shown, no server-side check. The form works exactly as before.

---

## Environment Variables

| Variable | Required | Description |
|----------|----------|-------------|
| `TURNSTILE_ENABLED` | No (default: `false`) | Set `true` to activate |
| `TURNSTILE_SITE_KEY` | When enabled | Public Cloudflare site key — safe to embed in HTML |
| `TURNSTILE_SECRET_KEY` | When enabled | Private server-side secret — **never in source code** |

### Dev bypass

Leave `TURNSTILE_ENABLED` unset or `false`. No Cloudflare calls, no widget.

### Production activation

```
TURNSTILE_ENABLED=true
TURNSTILE_SITE_KEY=0x4AAA...    # from Cloudflare dashboard
TURNSTILE_SECRET_KEY=0x4AAA...  # from Cloudflare dashboard, keep secret
```

---

## How to get the keys

1. Log in to [Cloudflare Dashboard](https://dash.cloudflare.com/)
2. Go to **Turnstile** → **Add site**
3. Enter domain: `masqueclima.es`
4. Widget type: **Managed** (recommended)
5. Copy Site Key → `TURNSTILE_SITE_KEY`
6. Copy Secret Key → `TURNSTILE_SECRET_KEY`

---

## Client-side

### Script injection

`patch_snapshot_html()` in `app/front_controller.php` injects the Turnstile JS only when enabled:

```html
<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
```

### Widget in quote modal

`patch_snapshot_quote_modal()` injects the widget div just before the submit button when enabled:

```html
<div class="cf-turnstile mb-3"
     data-sitekey="SITE_KEY"
     data-theme="light"
     data-language="es">  <!-- matches page language -->
</div>
```

The widget renders automatically when the script loads. On successful challenge, Cloudflare adds a hidden `cf-turnstile-response` field to the form.

---

## Server-side validation

In `public/contact-submit.php`, after CSRF check:

1. Read `TURNSTILE_ENABLED` env var (parsed as boolean)
2. If enabled but `TURNSTILE_SECRET_KEY` missing → **fail safe** (reject + error log, never expose)
3. Read `cf-turnstile-response` from `$_POST`
4. POST to `https://challenges.cloudflare.com/turnstile/v0/siteverify`
   - `secret` = `TURNSTILE_SECRET_KEY`
   - `response` = token from form
   - `remoteip` = `$_SERVER['REMOTE_ADDR']`
5. Parse JSON response; pass only if `success === true`
6. On failure → redirect with `?sent=2` (error modal shown)
7. On Cloudflare API unreachable → **fail safe** (reject)

### `verify_turnstile()` function

```php
function verify_turnstile(string $secret, string $token, string $remoteip): bool {
  // POSTs to Cloudflare siteverify; returns true only on explicit success
  // Fails safe if API unreachable
}
```

---

## Response codes

| `?sent=` | Meaning | UI |
|----------|---------|----|
| `1` | Success | thanksModal |
| `0` | Generic error (CSRF, validation, mail failure) | errorModal |
| `2` | Captcha/spam rejection | errorModal |

---

## Security notes

- `TURNSTILE_SECRET_KEY` is read exclusively from environment variables — never in source code or logs
- If secret is set but empty string at runtime → submission rejected + error logged
- Turnstile does NOT replace CSRF — both checks run independently
- Honeypot (`company` field) check runs before Turnstile

---

## Files changed

| File | Change |
|------|--------|
| `app/config.php` | Added `turnstile.enabled`, `turnstile.site_key`, `turnstile.secret_key` from env vars |
| `app/front_controller.php` | Injects Turnstile script in `patch_snapshot_html()`; injects widget in `patch_snapshot_quote_modal()` |
| `public/contact-submit.php` | Reads `TURNSTILE_ENABLED`, calls `verify_turnstile()` after CSRF check |

---

## Testing

### With Turnstile disabled (default dev)

```bash
# No env var set — form works as before
curl -X POST http://localhost:8787/contact-submit.php \
  -d "csrf=TOKEN&name=Test&phone=123456789"
```

### With Turnstile enabled (mock)

Cloudflare provides test keys for CI/dev:
- Site key: `1x00000000000000000000AA` (always passes)
- Secret: `1x0000000000000000000000000000000AA` (always passes)
- Site key: `2x00000000000000000000AB` (always blocks — for testing block UI)

Set these via env to test the full flow locally.
