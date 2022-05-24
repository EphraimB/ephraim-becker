FROM php:8.0-apache
COPY ./src/ /var/www/html/

RUN apt-get update -y && apt-get install -y libpng-dev libjpeg-dev

RUN apt-get update && \
    apt-get install -y \
        zlib1g-dev \
        libzip-dev \
        unzip \
        libssh2-1-dev libssh2-1

RUN docker-php-ext-configure gd --enable-gd --with-jpeg
RUN docker-php-ext-install mysqli gd exif zip

RUN pecl install ssh2-1.3.1 \
    && docker-php-ext-enable ssh2

WORKDIR /var/www/html/
COPY --from=composer /usr/bin/composer /usr/bin/composer
RUN composer install
