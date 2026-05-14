FROM dunglas/frankenphp:1-php8.3-bookworm

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        jpegoptim \
        optipng \
        pngquant \
        unzip \
        webp \
    && rm -rf /var/lib/apt/lists/*

RUN install-php-extensions \
    intl \
    pdo_mysql \
    gd \
    opcache

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . /app

RUN mkdir -p var public/uploads \
    && composer install --no-dev --optimize-autoloader --prefer-dist --no-interaction --no-scripts \
    && chown -R www-data:www-data /app/var /app/public/uploads

COPY docker/php/app.prod.ini $PHP_INI_DIR/conf.d/zz-app.prod.ini
COPY docker/entrypoint.sh /usr/local/bin/app-entrypoint

RUN chmod +x /usr/local/bin/app-entrypoint

ENV APP_ENV=prod
ENV APP_DEBUG=0
ENV SERVER_NAME=localhost
ENV SERVER_ROOT=/app/public

ENTRYPOINT ["app-entrypoint"]
CMD ["frankenphp", "run", "--config", "/app/Caddyfile"]
