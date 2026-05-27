#!/usr/bin/env bash
set -euo pipefail

APP_NAME="masqueclima-legacy-dev"
REPO_DIR="${REPO_DIR:-/home/debian/repos/masqueclima}"
APP_DIR="${APP_DIR:-/srv/apps/${APP_NAME}}"
BRANCH="${BRANCH:-fix/legacy-php-dev-stabilization}"
PHP_BIN="${PHP_BIN:-php}"
SKIP_GIT="${SKIP_GIT:-0}"

timestamp="$(date -u +%Y%m%d%H%M%S)"
release_dir="${APP_DIR}/releases/${timestamp}"
shared_dir="${APP_DIR}/shared"

echo "==> Preparing ${APP_NAME} release ${timestamp}"
mkdir -p "${APP_DIR}/releases" "${shared_dir}/logs"

if [ ! -f "${shared_dir}/.env" ]; then
  cat > "${shared_dir}/.env" <<'ENV'
APP_ENV=staging
CONTACT_LOG_DIR=/srv/apps/masqueclima-legacy-dev/shared/logs
SMTP_HOST=masqueclima.es
ENV
fi
chmod 640 "${shared_dir}/.env"
if command -v sudo >/dev/null 2>&1; then
  sudo chgrp www-data "${shared_dir}/.env" "${shared_dir}/logs" || true
  sudo chmod 2775 "${shared_dir}/logs" || true
else
  chmod 775 "${shared_dir}/logs"
fi

cd "${REPO_DIR}"
if [ "${SKIP_GIT}" != "1" ]; then
  git fetch origin
  git checkout "${BRANCH}"
  git pull --ff-only origin "${BRANCH}"
else
  echo "==> SKIP_GIT=1; using current working tree in ${REPO_DIR}"
fi

mkdir -p "${release_dir}"
rsync -a --delete \
  --exclude ".git/" \
  --exclude ".github/" \
  --exclude ".next/" \
  --exclude "node_modules/" \
  --exclude "logs/" \
  --exclude "storage/logs/" \
  --exclude ".env" \
  --exclude "*.tsbuildinfo" \
  ./ "${release_dir}/"

ln -sfn "${shared_dir}/.env" "${release_dir}/.env"
mkdir -p "${release_dir}/storage"
rm -rf "${release_dir}/storage/logs"
ln -sfn "${shared_dir}/logs" "${release_dir}/storage/logs"

echo "==> PHP syntax check"
find "${release_dir}/app" "${release_dir}/views" "${release_dir}/public" "${release_dir}/public_html" \
  -name "*.php" -not -path "*/vendor/*" -print0 | xargs -0 -n1 "${PHP_BIN}" -l >/tmp/${APP_NAME}-php-lint.log

ln -sfn "${release_dir}" "${APP_DIR}/current"

echo "==> Nginx validation and reload"
sudo nginx -t
php_fpm_units="$(systemctl list-units --type=service --all 'php*-fpm.service' --no-legend --no-pager 2>/dev/null | awk '{print $1}')"
if [ -n "${php_fpm_units}" ]; then
  for unit in ${php_fpm_units}; do
    sudo systemctl reload "${unit}" || sudo systemctl restart "${unit}"
  done
fi
sudo systemctl reload nginx

echo "==> HTTP validation"
base="https://dev.masqueclima.es"
root_code="$(curl -ksS -o /dev/null -w "%{http_code}" --max-redirs 0 "${base}/")"
root_location="$(curl -ksSI --max-redirs 0 "${base}/" | awk -F': ' 'tolower($1)=="location"{print $2}' | tr -d '\r')"
[ "${root_code}" = "301" ]
case "${root_location}" in
  /es/|"${base}/es/") ;;
  *) echo "Validation failed: / location was ${root_location}" >&2; exit 1 ;;
esac

for path in /es/ /en/ /de/ /nl/ /ru/ /no/ /robots.txt /sitemap.xml /assets/css/styles.css /assets/js/main.js /assets/img/hero.mp4; do
  code="$(curl -ksS -o /dev/null -w "%{http_code}" "${base}${path}")"
  [ "${code}" = "200" ] || { echo "Validation failed: ${path} returned ${code}" >&2; exit 1; }
done

curl -ksSI "${base}/es/" | grep -qi "X-Robots-Tag: noindex, nofollow, noarchive"
curl -ksS "${base}/robots.txt" | grep -q "Disallow: /"

echo "==> Active release: ${release_dir}"
