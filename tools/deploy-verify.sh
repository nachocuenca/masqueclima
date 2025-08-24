#!/bin/bash
# Verifica endpoints críticos tras el deploy
set -euo pipefail
BASE_URL="${1:-}"
if [[ -z "$BASE_URL" ]]; then
  echo "Uso: $0 https://dominio" >&2
  exit 1
fi

declare -A endpoints=(
  ["/"]="text/html"
  ["/en/"]="text/html"
  ["/robots.txt"]="text/plain"
  ["/sitemap.xml"]="application/xml"
  ["/assets/css/styles.css"]="text/css"
)

for path in "${!endpoints[@]}"; do
  expected="${endpoints[$path]}"
  read -r code ctype < <(curl -s -o /dev/null -w "%{http_code} %{content_type}" "$BASE_URL$path")
  echo "$path -> $code $ctype"
  if [[ "$code" != "200" ]]; then
    echo "Fallo: código $code en $path" >&2
    exit 1
  fi
  if [[ "$ctype" != "$expected"* ]]; then
    echo "Advertencia: tipo $ctype, se esperaba $expected" >&2
  fi
done

echo "Verificación completada"
