FROM php:8.0-apache
COPY ./src/ /var/www/html/

RUN apt-get update -y && apt-get install -y libpng-dev libjpeg-dev

RUN apt-get update && \
    apt-get install -y \
        zlib1g-dev \
        libzip-dev \
        unzip

RUN docker-php-ext-configure gd --enable-gd --with-jpeg
RUN docker-php-ext-install mysqli gd exif zip

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
RUN composer require google/apiclient:^2.0
