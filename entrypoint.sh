#!/bin/bash
set -e

echo "===> [1/6] Menyiapkan file .env untuk Render..."

# Tulis file .env secara langsung (tidak perlu sed yang kompleks)
cat > /var/www/html/.env << 'ENVEOF'
APP_NAME="Nota Kontan"
APP_ENV=production
APP_KEY=base64:2e3O9WkHIlwy5QMotxJ/a4KxrYrs3loZHNJBX/8PtlU=
APP_DEBUG=true
APP_URL=https://nota-kontan.onrender.com

LOG_CHANNEL=stderr
LOG_LEVEL=debug

DB_CONNECTION=sqlite
DB_DATABASE=/var/www/html/database/database.sqlite

SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
CACHE_STORE=file
ENVEOF

echo "===> [2/6] Membuat database SQLite..."
mkdir -p /var/www/html/database
touch /var/www/html/database/database.sqlite

echo "===> [3/6] Membuat folder storage yang dibutuhkan..."
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/framework/cache/data
mkdir -p /var/www/html/storage/logs

echo "===> [4/6] Mengatur izin akses (chmod)..."
chmod -R 777 /var/www/html/storage
chmod -R 777 /var/www/html/bootstrap/cache
chmod -R 777 /var/www/html/database
chmod 644 /var/www/html/.env

echo "===> [5/6] Menjalankan migrasi dan seeder database..."
cd /var/www/html
php artisan migrate --force
php artisan db:seed --force || echo "Seeder dilewati (mungkin sudah ada data)"
php artisan storage:link --force || true

echo "===> [6/6] Menyalakan Apache Web Server..."
# Sesuaikan port Apache jika Render menetapkan PORT
if [ -n "$PORT" ]; then
    echo "Mengatur Apache port: $PORT"
    sed -i "s/Listen 80/Listen $PORT/g" /etc/apache2/ports.conf
    sed -i "s/<VirtualHost \*:80>/<VirtualHost \*:$PORT>/g" /etc/apache2/sites-available/000-default.conf
fi

exec apache2-foreground
