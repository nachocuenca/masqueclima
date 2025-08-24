# Checklists

## Deploy
- [ ] Actualizar repo local (`git pull`).
- [ ] Ejecutar pruebas: `tools/deploy-verify.sh` en staging.
- [ ] Subir archivos a `public_html/masqueclima`.
- [ ] Copiar `.htaccess` raíz y de `public/`.
- [ ] Seleccionar PHP 8.2 en cPanel.
- [ ] Limpiar caché de LiteSpeed/CDN.
- [ ] Ejecutar `tools/deploy-verify.sh https://dominio`.

## Rollback
- [ ] Restaurar backup previo (`masqueclima` + `.htaccess`).
- [ ] Limpiar caché.
- [ ] Verificar `?__health=1`.
