# Plan de migración SEO-first

## Fase actual completada

- Auditoría del PHP local.
- Detección de 403 en `/` producción.
- Detección de deriva entre repo local y sitemap vivo.
- Exportación mecánica de contenido PHP a JSON.
- Next.js App Router con TypeScript, Tailwind, Metadata API y JSON-LD.
- Staging protegido contra indexación.
- Docker/Nginx/release scripts preparados.

## Principios

- Preservar primero, optimizar después.
- No inventar traducciones.
- No publicar en producción hasta comparar sitemap vivo completo.
- No generar `hreflang` para páginas sin equivalente real.
- No indexar `dev.masqueclima.es`.

## Secuencia recomendada

1. Validar local con `npm run lint` y `npm run build`.
2. Desplegar a `dev.masqueclima.es`.
3. Ejecutar `npm run seo:audit -- https://dev.masqueclima.es`.
4. Ejecutar `npm run seo:compare`.
5. Exportar sitemap completo vivo de producción y cerrar redirecciones.
6. Configurar Google Business Profile API para reseñas reales.
7. Revisar visual/contenido con cliente.
8. Solo después preparar cutover de producción.

## No hacer aún

- No cambiar DNS de `masqueclima.es`.
- No desplegar script prod.
- No enviar sitemap de dev a Google.
- No indexar dev.
- No activar traducciones automáticas de reseñas sin revisión legal/editorial.
