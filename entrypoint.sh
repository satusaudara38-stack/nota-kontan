#!/bin/bash
set -e

echo "===> Menyiapkan Aplikasi Nota Kontan di Render..."

# Sesuaikan port Apache jika Render menetapkan environment variable PORT
if [ -n "$PORT" ]; then
    echo "===> Mengatur Apache untuk port $PORT..."
    sed -i "s/Listen 80/Listen $PORT/g" /etc/apache2/ports.conf
    sed -i "s/<VirtualHost \*:80>/<VirtualHost \*:$PORT>/g" /etc/apache2/sites-available/000-default.conf
fi

# Pastikan file .env ada di container
if [ ! -f /var/www/html/.env ]; then
    echo "===> Menyiapkan file .env dari .env.example..."
    cp /var/www/html/.env.example /var/www/html/.env
fi

# Pastikan APP_KEY terisi otomatis jika belum ada
DEFAULT_KEY="base64:2e3O9WkHIlwy5QMotxJ/a4KxrYrs3loZHNJBX/8PtlU="
ACTUAL_KEY="${APP_KEY:-$DEFAULT_KEY}"
sed -i "s|^APP_KEY=.*|APP_KEY=$ACTUAL_KEY|g" /var/www/html/.env

# Konfigurasi database SQLite, session file, dan APP_DEBUG untuk diagnosa
sed -i 's|^DB_CONNECTION=.*|DB_CONNECTION=sqlite|g' /var/www/html/.env
sed -i 's|^# DB_DATABASE=.*|DB_DATABASE=/var/www/html/database/database.sqlite|g' /var/www/html/.env
sed -i 's|^DB_DATABASE=.*|DB_DATABASE=/var/www/html/database/database.sqlite|g' /var/www/html/.env
sed -i 's|^SESSION_DRIVER=.*|SESSION_DRIVER=file|g' /var/www/html/.env
sed -i 's|^CACHE_STORE=.*|CACHE_STORE=file|g' /var/www/html/.env
sed -i 's|^APP_ENV=.*|APP_ENV=production|g' /var/www/html/.env
sed -i 's|^APP_DEBUG=.*|APP_DEBUG=true|g' /var/www/html/.env
sed -i 's|^LOG_CHANNEL=.*|LOG_CHANNEL=stderr|g' /var/www/html/.env

# Pastikan folder dan file database SQLite ada
mkdir -p /var/www/html/database
if [ ! -f /var/www/html/database/database.sqlite ]; then
    echo "===> Membuat database.sqlite baru..."
    touch /var/www/html/database/database.sqlite
fi

# Pastikan folder sessions dan logs ada di storage
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/framework/cache
mkdir -p /var/www/html/storage/logs

# Hubungkan public storage symlink
php artisan storage:link --force || true

# Jalankan migrasi tabel dan seeder kontak pimpinan
echo "===> Menjalankan migrasi database..."
php artisan migrate --force || true
php artisan db:seed --force || true

# Bersihkan semua cache lama agar membaca konfigurasi baru
php artisan config:clear || true
php artisan cache:clear || true
php artisan route:clear || true
php artisan view:clear || true

# Berikan izin penuh kepada user www-data (Apache) SETELAH semua file dibuat
echo "===> Mengatur permission untuk user www-data..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database /var/www/html/.env
chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database

echo "===> Aplikasi siap! Menjalankan Apache Web Server..."
exec apache2-foreground
