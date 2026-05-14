#!/bin/sh
set -eu

cd /app

if [ ! -f .env ] && [ -f .env.prod.example ]; then
    cp .env.prod.example .env
fi

mkdir -p \
    var \
    public/uploads \
    public/uploads/media \
    public/uploads/media/drinks \
    public/uploads/media/foods \
    public/uploads/media/events
chown -R www-data:www-data var public/uploads || true

php bin/console cache:clear --env=prod --no-debug
php bin/console cache:warmup --env=prod --no-debug

if [ "${AUTO_SCHEMA_UPDATE:-1}" = "1" ]; then
    php bin/console doctrine:schema:update --force --env=prod --no-debug
fi

if [ "${OPTIMIZE_IMAGES_ON_BOOT:-1}" = "1" ]; then
    php bin/console app:optimize-images --env=prod || true
fi

exec docker-php-entrypoint "$@"
