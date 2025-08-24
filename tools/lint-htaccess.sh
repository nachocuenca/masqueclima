#!/bin/bash
# Linter básico para archivos .htaccess
set -euo pipefail
files=("public_html/.htaccess" "public/.htaccess")
for f in "${files[@]}"; do
  if [[ ! -f "$f" ]]; then
    echo "Falta $f" >&2
    exit 1
  fi
  echo "Revisando $f"
  grep -q "RewriteEngine" "$f" || echo "  ⚠️  Falta RewriteEngine On" >&2
  if [[ "$f" == "public/.htaccess" ]]; then
    grep -q "DirectoryIndex index.php" "$f" || echo "  ⚠️  Falta DirectoryIndex" >&2
    grep -q "AddDefaultCharset utf-8" "$f" || echo "  ⚠️  Falta AddDefaultCharset" >&2
  fi
done

echo "Lint terminado"
