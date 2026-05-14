# Deploy produzione

## Deploy manuale da terminale

Flusso previsto:

1. preparare e verificare le modifiche in locale;
2. fare commit e push su GitHub;
3. collegarsi al server e lanciare il deploy.

### 1. Controlli locali

Dalla cartella del progetto:

```bash
git status
composer prod:check
composer prod:cache
```

Se hai modificato immagini o contenuti visuali:

```bash
composer images:optimize
```

### 2. Commit e push

Controlla i file modificati:

```bash
git status
```

Aggiungi le modifiche:

```bash
git add .
```

Crea il commit:

```bash
git commit -m "Descrizione breve delle modifiche"
```

Invia tutto sul repository remoto:

```bash
git push origin main
```

### 3. Deploy sul server

Collegati al server:

```bash
ssh utente@server
```

Entra nella cartella del progetto:

```bash
cd /percorso/del/progetto
```

Scarica l'ultima versione dal branch `main`:

```bash
git pull origin main
```

Se il progetto gira con Docker Compose, lancia il deploy:

```bash
./deploy/nipio-up.sh
```

In alternativa, se vuoi usare direttamente Docker Compose:

```bash
set -a
. deploy/.env.nipio.local
set +a
docker compose -f docker-compose.nipio.yml up -d --build
```

Installa le dipendenze di produzione e rigenera l'ambiente:

```bash
composer install --no-dev --optimize-autoloader
composer dump-env prod
php bin/console app:optimize-images --env=prod
php bin/console cache:clear --env=prod --no-debug
php bin/console cache:warmup --env=prod --no-debug
```

Se ci sono modifiche alle entita o al database, prima controlla l'SQL:

```bash
php bin/console doctrine:schema:update --dump-sql --env=prod --no-debug
```

Se l'SQL e corretto, applicalo:

```bash
php bin/console doctrine:schema:update --force --env=prod --no-debug
```

Sistema i permessi delle cartelle scrivibili:

```bash
mkdir -p var public/uploads
chmod -R ug+rwX var public/uploads
```

Verifica finale:

```bash
composer prod:check
```

Controlla dal browser che `/`, `/menu`, `/food`, `/login` e `/admin` rispondano correttamente.

### Comando unico da lanciare via SSH

Quando il server e gia configurato, puoi lanciare il deploy con un solo comando dal terminale locale:

```bash
ssh utente@server 'cd /percorso/del/progetto && git pull origin main && composer install --no-dev --optimize-autoloader && composer dump-env prod && php bin/console app:optimize-images --env=prod && php bin/console cache:clear --env=prod --no-debug && php bin/console cache:warmup --env=prod --no-debug && composer prod:check'
```

Se il deploy include modifiche al database, esegui prima il comando con `--dump-sql` e applica `--force` solo dopo aver controllato l'SQL.

## Prima configurazione Git sul server esistente

Se sul server la cartella esiste gia ma `git pull` restituisce:

```text
fatal: not a git repository (or any of the parent directories): .git
```

significa che quella cartella non e un clone Git. Prima salva i file locali importanti:

```bash
cd ~
cp -a menu menu.backup.$(date +%Y%m%d-%H%M%S)
```

Poi collega la cartella al repository:

```bash
cd ~/menu
git init
git remote add origin https://github.com/Luca24111/moloko.git
git fetch origin main
git reset --mixed origin/main
```

Il comando `git reset --mixed` collega la cartella al branch remoto senza cancellare i file locali. Controlla poi lo stato:

```bash
git status
```

Se vedi solo file locali da ignorare, come `deploy/.env.nipio.local`, puoi procedere con:

```bash
git pull origin main
./deploy/nipio-up.sh
```

Non committare mai `deploy/.env.nipio.local`: contiene i valori reali di produzione ed e ignorato da Git.

## Requisiti

- PHP 8.1 o superiore.
- Estensioni PHP `ctype`, `gd`, `iconv`, `pdo_mysql`.
- Web server con document root puntata a `public/`.
- Database MySQL/MariaDB raggiungibile dal server.
- Cartelle scrivibili dal processo PHP: `var/` e `public/uploads/`.

## Variabili ambiente

Configura sul server variabili reali, oppure crea un file non versionato `.env.local` partendo da `.env.prod.example`.

In locale usa `.env.local` per lo sviluppo. Questo file e ignorato da Git e puo contenere valori DEV come:

```dotenv
APP_ENV=dev
APP_DEBUG=1
```

In produzione non usare il `.env.local` del tuo PC: il server deve mantenere il proprio `.env.local` con valori reali di produzione, oppure variabili ambiente configurate a livello server.

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
