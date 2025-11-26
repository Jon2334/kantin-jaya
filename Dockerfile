# Menggunakan PHP versi 8.2 dengan Apache
FROM php:8.2-apache

# Install driver yang dibutuhkan untuk Neon (PostgreSQL) dan unzip
RUN apt-get update && apt-get install -y \
    libpq-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-install pdo_pgsql bcmath

# Mengaktifkan mod_rewrite untuk URL Laravel agar cantik
RUN a2enmod rewrite

# Mengubah root folder Apache ke folder /public Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf | true

# Copy Composer (alat install library PHP)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set folder kerja
WORKDIR /var/www/html

# Copy semua file project ke dalam Docker
COPY . .

# Install library Laravel
RUN composer install --no-dev --optimize-autoloader

# Atur hak akses agar Laravel bisa tulis ke folder storage
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache