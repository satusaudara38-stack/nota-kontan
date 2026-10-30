FROM richarvey/nginx-php-fpm:latest

WORKDIR /var/www/html

# Salin seluruh file proyek
COPY . .

# Salin script entrypoint dan bersihkan line-ending Windows (CRLF -> LF)
COPY entrypoint.sh /entrypoint.sh
RUN sed -i -e 's/\r$//' /entrypoint.sh && chmod +x /entrypoint.sh

# Install dependensi PHP Composer saat build image
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Konfigurasi Environment Container
ENV WEBROOT=/var/www/html/public
ENV PHP_ERRORS_STDERR=1
ENV RUN_SCRIPTS=1
ENV REAL_IP_HEADER=1
ENV APP_ENV=production
ENV APP_DEBUG=false
ENV LOG_CHANNEL=stderr
ENV COMPOSER_ALLOW_SUPERUSER=1

# Port standar Render
EXPOSE 10000 80

CMD ["/entrypoint.sh"]
