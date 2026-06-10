
FROM php:8.2-fpm

# Install dependencies sistem & alat compile
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev \
    zip unzip libzip-dev autoconf build-essential

# Bersihkan cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Ekstensi PHP + Opcache + Redis
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip opcache \
    && pecl install redis \
    && docker-php-ext-enable redis

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Setup Working Directory
WORKDIR /var/www
COPY . /var/www

# Permission Folder
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage \
    && chmod -R 775 /var/www/bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]
