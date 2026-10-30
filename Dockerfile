FROM php:8.3-apache

# Install ekstensi PHP & dependensi sistem yang dibutuhkan Laravel
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libsqlite3-dev \
    unzip \
    git \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_sqlite zip \
    && a2enmod rewrite \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer resmi
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Ubah Apache Document Root ke /var/www/html/public
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

WORKDIR /var/www/html

# Salin file proyek
COPY . .

# Salin script entrypoint dan bersihkan line-ending Windows (CRLF -> LF)
COPY entrypoint.sh /entrypoint.sh
RUN sed -i -e 's/\r$//' /entrypoint.sh && chmod +x /entrypoint.sh

# Install dependensi PHP Composer saat build image (dengan PHP 8.3 asli)
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Environment produksi
ENV APP_ENV=production
ENV APP_DEBUG=false
ENV LOG_CHANNEL=stderr

EXPOSE 80 10000

CMD ["/entrypoint.sh"]
