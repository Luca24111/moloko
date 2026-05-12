# Deploy produzione

## Requisiti

- PHP 8.1 o superiore.
- Estensioni PHP `ctype`, `gd`, `iconv`, `pdo_mysql`.
- Web server con document root puntata a `public/`.
- Database MySQL/MariaDB raggiungibile dal server.
- Cartelle scrivibili dal processo PHP: `var/` e `public/uploads/`.

## Variabili ambiente

Configura sul server variabili reali, oppure crea un file non versionato `.env.local` partendo da `.env.prod.example`.

Valori obbligatori:

```dotenv
APP_ENV=prod
APP_DEBUG=0
APP_SECRET=replace-with-a-long-random-secret
DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=8.0&charset=utf8mb4"
APP_ADMIN_USER=admin@example.com
APP_ADMIN_PASSWORD=replace-with-a-strong-password
```

Non inserire credenziali reali in `.env`, perche e un file versionato.

## Build

```bash
composer install --no-dev --optimize-autoloader
composer dump-env prod
php bin/console app:optimize-images --env=prod
php bin/console cache:clear --env=prod --no-debug
php bin/console cache:warmup --env=prod --no-debug
```

## Database

Il progetto non usa migrazioni versionate. Prima di applicare modifiche in produzione controlla l'SQL:

```bash
php bin/console doctrine:schema:update --dump-sql --env=prod --no-debug
```

Poi applica solo se l'SQL e coerente:

```bash
php bin/console doctrine:schema:update --force --env=prod --no-debug
```

## Permessi

```bash
mkdir -p var public/uploads
chmod -R ug+rwX var public/uploads
```

## Verifica

```bash
composer prod:check
composer prod:cache
```

Controlla che `/`, `/menu`, `/food`, `/login` e `/admin` rispondano correttamente.
