#!/usr/bin/env bash
set -Eeuo pipefail

APP_NAME="masqueclima-dev"
REPO_DIR="/home/debian/repos/masqueclima"
APP_DIR="/srv/apps/${APP_NAME}"
BRANCH="${BRANCH:-feature/next-vps-seo-rebuild}"
RELEASE_ID="$(date -u +%Y%m%d%H%M%S)"
RELEASE_DIR="${APP_DIR}/releases/${RELEASE_ID}"

require_cmd() {
  command -v "$1" >/dev/null 2>&1 || {
    echo "Missing dependency: $1" >&2
    exit 1
  }
}

docker_compose() {
  if docker info >/dev/null 2>&1; then
    docker compose "$@"
  else
    sudo docker compose "$@"
  fi
}

require_cmd git
require_cmd rsync
require_cmd docker
require_cmd curl
require_cmd sudo

cd "$REPO_DIR"
git fetch origin "$BRANCH"
git checkout "$BRANCH"
git pull --ff-only origin "$BRANCH"

mkdir -p "${APP_DIR}/releases" "${APP_DIR}/shared/logs"
mkdir -p "$RELEASE_DIR"

rsync -a --delete \
  --exclude=".git" \
  --exclude="node_modules" \
  --exclude=".next" \
  --exclude=".env" \
  --exclude=".env.*" \
  --exclude="logs" \
  --exclude="storage" \
  "$REPO_DIR"/ "$RELEASE_DIR"/

if [ -f "${APP_DIR}/shared/.env" ]; then
  ln -sfn "${APP_DIR}/shared/.env" "${RELEASE_DIR}/.env"
else
  echo "WARN: ${APP_DIR}/shared/.env does not exist. Create it from .env.example before production-like validation." >&2
fi

cp "${RELEASE_DIR}/docker-compose.dev.yml" "${APP_DIR}/docker-compose.yml"
ln -sfn "$RELEASE_DIR" "${APP_DIR}/current.tmp"
mv -Tf "${APP_DIR}/current.tmp" "${APP_DIR}/current"

cd "$APP_DIR"
docker_compose -p "$APP_NAME" -f docker-compose.yml up -d --build --remove-orphans

curl -fsS "http://127.0.0.1:18110/api/health/" >/dev/null
curl -fsS "https://dev.masqueclima.es/api/health/" >/dev/null
curl -fsSI "https://dev.masqueclima.es/es/" | grep -qi "x-robots-tag: noindex"
curl -fsS "https://dev.masqueclima.es/robots.txt" | grep -q "Disallow: /"

echo "Active release: ${RELEASE_ID}"
readlink -f "${APP_DIR}/current"
