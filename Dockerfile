FROM php:8.2-fpm-alpine


RUN set -ex \
    && apk --no-cache add postgresql-dev nodejs yarn \
    && docker-php-ext-install pdo pdo_pgsql

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html
