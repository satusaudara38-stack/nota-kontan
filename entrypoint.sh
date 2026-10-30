#!/bin/bash
set -e

echo "===> Menyiapkan Aplikasi Nota Kontan di Render..."

# Sesuaikan port Apache jika Render menetapkan environment variable PORT
if [ -n "$PORT" ]; then
    echo "===> Mengatur Apache untuk port $PORT..."
    sed -i "s/Listen 80/Listen $PORT/g" /etc/apache2/ports.conf
    sed -i "s/<VirtualHost \*:80>/<VirtualHost \*:$PORT>/g" /etc/apache2/sites-available/000-default.conf
fi

# Pastikan folder dan database SQLite ada
mkdir -p /var/www/html/database
if [ ! -f /var/www/html/database/database.sqlite ]; then
    echo "===> Membuat database.sqlite baru..."
    touch /var/www/html/database/database.sqlite
fi

# Set permission storage, cache, dan database
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
