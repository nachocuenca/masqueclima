#!/usr/bin/env bash
set -euo pipefail

APP_NAME="masqueclima-legacy-dev"
APP_DIR="${APP_DIR:-/srv/apps/${APP_NAME}}"
TARGET_RELEASE="${1:-}"

if [ -z "${TARGET_RELEASE}" ]; then
  echo "Usage: $0 <release-timestamp|absolute-release-path>" >&2
  echo "Available releases:" >&2
  ls -1 "${APP_DIR}/releases" >&2
  exit 2
fi

if [[ "${TARGET_RELEASE}" = /* ]]; then
  release_dir="${TARGET_RELEASE}"
else
  release_dir="${APP_DIR}/releases/${TARGET_RELEASE}"
fi

if [ ! -d "${release_dir}/public" ]; then
  echo "Invalid release: ${release_dir}" >&2
  exit 1
fi

ln -sfn "${release_dir}" "${APP_DIR}/current"

sudo nginx -t
sudo systemctl reload nginx

base="https://dev.masqueclima.es"
code="$(curl -ksS -o /dev/null -w "%{http_code}" "${base}/es/")"
[ "${code}" = "200" ] || { echo "Rollback health failed: /es/ returned ${code}" >&2; exit 1; }

echo "Rolled back ${APP_NAME} to ${release_dir}"
