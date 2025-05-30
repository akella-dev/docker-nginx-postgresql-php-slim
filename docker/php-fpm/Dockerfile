FROM php:8.3-fpm-alpine

WORKDIR /www

# Устанавливаем зависимости
RUN apk add --no-cache \
    postgresql-dev \
    postgresql-client

RUN docker-php-ext-install pdo pdo_pgsql
RUN apk add --no-cache \
    git \
    unzip 

# Устанавливаем Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
