# Despliegue en Nicalia (cPanel/Apache)

## Requisitos previos
- Hosting con cPanel y PHP 8.2.
- Acceso FTP o File Manager.
- Dominio apuntado a la carpeta `public_html/`.

## Estructura de carpetas
```
public_html/
  .htaccess            ← reglas de entrada
  masqueclima/         ← repo completo
    public/            ← `index.php`, assets, robots, sitemap
    ...
```

## Pasos
1. **Subir código**
   - Copiar el contenido del repositorio al servidor dentro de `public_html/masqueclima/`.
   - Mantener `public_html/.htaccess` al mismo nivel que la carpeta `masqueclima`.
2. **Seleccionar versión de PHP**
   - En cPanel → *Select PHP Version* → elegir `8.2` y habilitar `mbstring`, `json`, `openssl`.
3. **Configurar `.htaccess`**
   - `public_html/.htaccess`: reglas raíz para redirigir al front controller.
   - `masqueclima/public/.htaccess`: reglas internas, idiomas y caché.
4. **Limpiar caché**
   - Si usa caché de Nicalia, limpiar desde cPanel → *LiteSpeed Cache*.
   - Borrar cache del navegador tras despliegue.
5. **Verificación post-deploy**
   - Ejecutar `tools/deploy-verify.sh https://tu-dominio`.
   - Revisar logs de error en `cPanel → Metrics → Errors`.
   - Comprobar que `https://tu-dominio/?__health=1` responde `OK`.

## Rollback
- Conservar copia del commit anterior.
- Para deshacer, sustituir la carpeta `masqueclima` por la versión previa y volver a subir `.htaccess` original.
