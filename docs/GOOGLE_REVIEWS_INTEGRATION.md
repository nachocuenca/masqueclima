# Integración oficial de reseñas Google

Estado: preparada a nivel de código, pendiente de credenciales OAuth y validación con la cuenta propietaria.

## Objetivo

Mostrar reseñas reales de Google con tarjetas grandes, sin inventar textos y sin depender de Elfsight como solución final.

## API oficial

Google Business Profile API permite listar reseñas de una ubicación verificada con:

```text
GET https://mybusiness.googleapis.com/v4/accounts/{accountId}/locations/{locationId}/reviews
```

La respuesta incluye:

- `reviews[]`
- `averageRating`
- `totalReviewCount`
- `reviewer.displayName`
- `reviewer.profilePhotoUrl`
- `starRating`
- `comment`
- `createTime`
- `updateTime`

Documentación oficial:

- https://developers.google.com/my-business/reference/rest/v4/accounts.locations.reviews
- https://developers.google.com/my-business/reference/rest/v4/accounts.locations.reviews/list
- https://developers.google.com/my-business/content/overview
- https://developers.google.com/my-business/reference/rest/v4/accounts.locations.media

## Variables preparadas

```bash
GOOGLE_BUSINESS_ACCOUNT_ID=
GOOGLE_BUSINESS_LOCATION_ID=
GOOGLE_BUSINESS_CLIENT_ID=
GOOGLE_BUSINESS_CLIENT_SECRET=
GOOGLE_BUSINESS_REFRESH_TOKEN=
GOOGLE_BUSINESS_REVIEW_PAGE_URL=
```

## Implementación actual

- `lib/google-business.ts` refresca OAuth y consulta reseñas.
- `components/sections/Reviews.tsx` muestra tarjetas grandes si hay datos oficiales.
- Si faltan credenciales, se conserva Elfsight como fallback temporal para no dejar vacío el bloque.

## Traducciones

La API de reseñas devuelve el comentario de la reseña como texto. No se debe inventar ni traducir manualmente sin aprobación. Si se quiere traducción automática por idioma, debe decidirse aparte con Google Cloud Translation u otro flujo revisado, idealmente mostrando el original o marcando que es traducción automática.

## Pendiente

- Crear proyecto Google Cloud.
- Habilitar Business Profile APIs.
- Obtener OAuth refresh token de una cuenta propietaria/administradora.
- Confirmar `accountId` y `locationId`.
- Validar cache, límites y fallback.
- Decidir si también se listan fotos del perfil mediante Media API.
