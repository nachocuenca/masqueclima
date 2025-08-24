# Configuración de entorno

## Variables
- `APP_ENV` – `production` o `development` (afecta `?__dbg=1`).
- `BRAND_DOMAIN` – dominio base usado por `base_url()`.
- `GA4_ID` – identificador opcional de Google Analytics 4.
- `SMTP_HOST`, `SMTP_USER`, `SMTP_PASS` – opcionales para PHPMailer si se habilita envío.

Las variables pueden definirse en `config/*.php` o mediante `.env` si se adapta.

## Entornos típicos
| Entorno | Dominio | Notas |
|---------|---------|-------|
| Desarrollo | `http://localhost` | `APP_ENV=development`, permite `?__dbg=1` |
| Producción | `https://masqueclima.com` | `APP_ENV=production`, GA4 opcional |
