#!/bin/bash
set -e

# Buat folder dan file database SQLite jika belum ada
mkdir -p /var/www/html/database
if [ ! -f /var/www/html/database/database.sqlite ]; then
    touch /var/www/html/database/database.sqlite
fi

# Set permission storage dan cache
chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database

# Hubungkan symlink storage
php artisan storage:link --force || true

# Jalankan migrasi dan seed database pimpinan
php artisan migrate --force || true
php artisan db:seed --force || true

# Optimasi cache Laravel
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Jalankan web server Nginx & PHP-FPM bawaan container
exec /start.sh
