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

# Konfigurasi database SQLite & Environment produksi di .env
sed -i 's|^DB_CONNECTION=.*|DB_CONNECTION=sqlite|g' /var/www/html/.env
sed -i 's|^# DB_DATABASE=.*|DB_DATABASE=/var/www/html/database/database.sqlite|g' /var/www/html/.env
sed -i 's|^DB_DATABASE=.*|DB_DATABASE=/var/www/html/database/database.sqlite|g' /var/www/html/.env
sed -i 's|^APP_ENV=.*|APP_ENV=production|g' /var/www/html/.env
sed -i 's|^APP_DEBUG=.*|APP_DEBUG=false|g' /var/www/html/.env

# Pastikan folder dan database SQLite ada
mkdir -p /var/www/html/database
if [ ! -f /var/www/html/database/database.sqlite ]; then
    echo "===> Membuat database.sqlite baru..."
    touch /var/www/html/database/database.sqlite
fi

# Set permission dan kepemilikan user www-data (Apache)
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database /var/www/html/.env
chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database

# Hubungkan public storage symlink
php artisan storage:link --force || true

# Jalankan migrasi tabel dan seeder kontak pimpinan
echo "===> Menjalankan migrasi database..."
php artisan migrate --force || true
php artisan db:seed --force || true

# Optimasi cache Laravel
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

echo "===> Aplikasi siap! Menjalankan Apache Web Server..."
exec apache2-foreground
