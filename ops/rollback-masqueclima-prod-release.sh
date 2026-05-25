#!/usr/bin/env bash
set -Eeuo pipefail

if [ "${CONFIRM_PROD_ROLLBACK:-}" != "yes" ]; then
  echo "Production rollback is disabled unless CONFIRM_PROD_ROLLBACK=yes is set." >&2
  exit 1
fi

APP_NAME="masqueclima"
APP_DIR="/srv/apps/${APP_NAME}"
RELEASE_INPUT="${1:-${RELEASE_ID:-}}"

if [ -z "$RELEASE_INPUT" ]; then
  echo "Usage: RELEASE_ID=<id> $0 or $0 /srv/apps/masqueclima/releases/<id>" >&2
  exit 1
fi

if [[ "$RELEASE_INPUT" = /* ]]; then
  RELEASE_DIR="$RELEASE_INPUT"
else
  RELEASE_DIR="${APP_DIR}/releases/${RELEASE_INPUT}"
fi

if [ ! -d "$RELEASE_DIR" ]; then
  echo "Release not found: $RELEASE_DIR" >&2
  exit 1
fi

docker_compose() {
  if docker info >/dev/null 2>&1; then
    docker compose "$@"
  else
    sudo docker compose "$@"
  fi
}

cp "${RELEASE_DIR}/docker-compose.yml" "${APP_DIR}/docker-compose.yml"
ln -sfn "$RELEASE_DIR" "${APP_DIR}/current.tmp"
mv -Tf "${APP_DIR}/current.tmp" "${APP_DIR}/current"

cd "$APP_DIR"
docker_compose -p "$APP_NAME" -f docker-compose.yml up -d --build --remove-orphans

curl -fsS "http://127.0.0.1:18111/api/health/" >/dev/null
curl -fsS "https://masqueclima.es/api/health/" >/dev/null

echo "Rolled back to:"
readlink -f "${APP_DIR}/current"
