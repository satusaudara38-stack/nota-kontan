#!/bin/bash
set -e

echo "===> Menyiapkan Aplikasi Nota Kontan di Render..."

# Pastikan folder dan database SQLite ada
mkdir -p /var/www/html/database
if [ ! -f /var/www/html/database/database.sqlite ]; then
    echo "===> Membuat database.sqlite baru..."
    touch /var/www/html/database/database.sqlite
fi

# Pastikan dependensi vendor terinstall
if [ ! -f /var/www/html/vendor/autoload.php ]; then
    echo "===> Menginstall dependensi Composer..."
    composer install --no-dev --optimize-autoloader --no-interaction
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

echo "===> Aplikasi siap! Menjalankan Web Server..."
exec /start.sh
