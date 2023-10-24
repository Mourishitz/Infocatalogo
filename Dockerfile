FROM php:8.1-fpm

ENV DEBIAN_FRONTEND noninteractive

ARG PHP_VERSION=8.1

# Updating OS
RUN apt-get update \
    && apt-get install -y \
        zlib1g-dev \
        libicu-dev \
        libgif-dev \
        libjpeg-dev \
        libcairo2-dev \
        libpango1.0-dev \
        libpng-dev \
        libpq-dev \
        libmcrypt-dev \
        libpng-dev \
        libzip-dev \
        zip \
        build-essential \
        g++ \
        libxml2-dev \
        vim \
        awscli \
        supervisor \
	    tzdata \
	    libjpeg62-turbo-dev \
	    cron \
        make \
    && pecl upgrade timezonedb.tgz \
    && pecl install swoole \
    && docker-php-ext-configure intl \
    && docker-php-ext-enable timezonedb \
    && docker-php-ext-enable swoole \
    && docker-php-ext-configure zip \
    && docker-php-ext-configure soap --enable-soap \
    && docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ \
    && docker-php-ext-install pcntl intl zip gd opcache pdo_pgsql soap \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && chmod +x /usr/local/bin/composer

RUN usermod -u 1000 www-data && groupmod -g 1000 www-data

ADD . /var/www/app

WORKDIR /var/www/app

RUN chown -R www-data:www-data /var/www/app

RUN mkdir /run/php

COPY docker/php/custom_php.ini /usr/local/etc/php/conf.d

COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

RUN sed -i "s/#PHP_VERSION#/${PHP_VERSION}/g" /etc/supervisor/conf.d/supervisord.conf

RUN chown -R www-data:www-data /var/log/supervisor/

EXPOSE 80

CMD ["/bin/bash", "./docker/entrypoint.sh"]
