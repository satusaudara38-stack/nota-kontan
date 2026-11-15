#!/bin/bash
set -e

# Deteksi konfigurasi database (PostgreSQL Render / MySQL / SQLite fallback)
if [ -n "$DATABASE_URL" ]; then
    if [[ "$DATABASE_URL" == postgres* ]] || [[ "$DATABASE_URL" == pgsql* ]]; then
        ACTIVE_DB="pgsql"
    elif [[ "$DATABASE_URL" == mysql* ]]; then
        ACTIVE_DB="mysql"
    else
        ACTIVE_DB="${DB_CONNECTION:-pgsql}"
    fi
elif [ -n "$DB_CONNECTION" ]; then
    ACTIVE_DB="$DB_CONNECTION"
else
    ACTIVE_DB="sqlite"
fi

ACTIVE_APP_ENV="${APP_ENV:-production}"
ACTIVE_APP_DEBUG="${APP_DEBUG:-false}"
ACTIVE_APP_KEY="${APP_KEY:-base64:2e3O9WkHIlwy5QMotxJ/a4KxrYrs3loZHNJBX/8PtlU=}"
ACTIVE_APP_URL="${APP_URL:-https://nota-kontan.onrender.com}"

echo "===> [1/6] Menyiapkan file .env (DB_CONNECTION=$ACTIVE_DB)..."

cat > /var/www/html/.env << EOF
APP_NAME="Nota Kontan"
APP_ENV=${ACTIVE_APP_ENV}
APP_KEY=${ACTIVE_APP_KEY}
APP_DEBUG=${ACTIVE_APP_DEBUG}
APP_URL=${ACTIVE_APP_URL}

LOG_CHANNEL=stderr
LOG_LEVEL=info

DB_CONNECTION=${ACTIVE_DB}
DATABASE_URL=${DATABASE_URL:-}
DB_HOST=${DB_HOST:-127.0.0.1}
DB_PORT=${DB_PORT:-5432}
DB_DATABASE=${DB_DATABASE:-/var/www/html/database/database.sqlite}
DB_USERNAME=${DB_USERNAME:-}
DB_PASSWORD=${DB_PASSWORD:-}

SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
CACHE_STORE=file
EOF

if [ "$ACTIVE_DB" = "sqlite" ]; then
    echo "===> [2/6] Menyiapkan database SQLite lokal..."
    mkdir -p /var/www/html/database
    if [ ! -f /var/www/html/database/database.sqlite ]; then
        touch /var/www/html/database/database.sqlite
    fi
else
    echo "===> [2/6] Menggunakan database eksternal permanen ($ACTIVE_DB)..."
fi

echo "===> [3/6] Membuat folder storage yang dibutuhkan..."
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/framework/cache/data
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/storage/app/public/bukti_pembayaran

echo "===> [4/6] Mengatur izin akses (chmod)..."
chmod -R 777 /var/www/html/storage
chmod -R 777 /var/www/html/bootstrap/cache
chmod -R 777 /var/www/html/database || true
chmod 644 /var/www/html/.env

echo "===> [5/6] Menjalankan migrasi dan seeder database..."
cd /var/www/html
php artisan migrate --force
php artisan db:seed --force || echo "Seeder dilewati (mungkin sudah ada data default)"
php artisan storage:link --force || true

echo "===> [6/6] Menyalakan Apache Web Server..."
if [ -n "$PORT" ]; then
    echo "Mengatur Apache port: $PORT"
    sed -i "s/Listen 80/Listen $PORT/g" /etc/apache2/ports.conf
    sed -i "s/<VirtualHost \*:80>/<VirtualHost \*:$PORT>/g" /etc/apache2/sites-available/000-default.conf
fi

exec apache2-foreground
