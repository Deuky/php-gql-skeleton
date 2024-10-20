FROM php:8.2-fpm AS base

WORKDIR /var/www/html

# Install dependencies
RUN apt-get update \
    && apt-get install -y unzip zip \
    && rm -rf /var/lib/apt/lists/* /var/cache/apt/archives/*

RUN docker-php-ext-install mysqli pdo pdo_mysql

COPY ./php/index.php /var/www/html/public/index.php


################## Build image ##################
FROM base AS build

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER=1


################## Development image ##################
FROM build AS development

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# Should be put in the mutlistage dockerfile as dev base.
COPY ./debug/xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
COPY ./debug/error_reporting.ini /usr/local/etc/php/conf.d/error_reporting.ini


################## Production image ##################
FROM base AS production

COPY ./src /var/www/html/src
COPY ./vendor /var/www/html/vendor
