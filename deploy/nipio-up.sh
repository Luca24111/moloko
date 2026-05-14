#!/bin/sh
set -eu

ROOT_DIR=$(CDPATH= cd -- "$(dirname "$0")/.." && pwd)
ENV_FILE="$ROOT_DIR/deploy/.env.nipio.local"

if [ -f "$ENV_FILE" ]; then
    set -a
    . "$ENV_FILE"
    set +a
fi

if [ -z "${PUBLIC_IP:-}" ]; then
    echo "PUBLIC_IP non impostato. Copia deploy/.env.nipio.example in deploy/.env.nipio.local e inserisci l'IP pubblico." >&2
    exit 1
fi

if [ -z "${APP_SLUG:-}" ]; then
    APP_SLUG="moloch"
fi

APP_HOST="${APP_SLUG}-$(printf '%s' "$PUBLIC_IP" | tr '.' '-').nip.io"

if [ -z "${APP_SECRET:-}" ]; then
    APP_SECRET=$(openssl rand -hex 16 2>/dev/null || date +%s | shasum | awk '{print $1}')
fi

export APP_HOST
export APP_SECRET
export APP_ADMIN_USER="${APP_ADMIN_USER:-admin@example.com}"
export APP_ADMIN_PASSWORD="${APP_ADMIN_PASSWORD:-change-me-now}"
export MYSQL_DATABASE="${MYSQL_DATABASE:-menu}"
export MYSQL_USER="${MYSQL_USER:-menu}"
export MYSQL_PASSWORD="${MYSQL_PASSWORD:-change-this-password}"
export MYSQL_ROOT_PASSWORD="${MYSQL_ROOT_PASSWORD:-change-this-root-password}"
export AUTO_SCHEMA_UPDATE="${AUTO_SCHEMA_UPDATE:-0}"
export OPTIMIZE_IMAGES_ON_BOOT="${OPTIMIZE_IMAGES_ON_BOOT:-0}"

cd "$ROOT_DIR"

docker compose -f docker-compose.nipio.yml up -d --build

echo "Deploy avviato."
echo "URL: https://$APP_HOST"
