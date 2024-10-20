FROM php:8.2-fpm AS base
ENV INDEX_FILE="react/index.php"

WORKDIR /var/www/html

# Install dependencies
RUN apt-get update \
    && apt-get install -y unzip zip \
    && rm -rf /var/lib/apt/lists/* /var/cache/apt/archives/*

RUN docker-php-ext-install mysqli pdo pdo_mysql

EXPOSE 80


FROM base AS build

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER=1


################## Development image ##################
FROM build AS development
ENV INDEX_FILE="react/index.php"

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# Should be put in the mutlistage dockerfile as dev base.
COPY ./debug/xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
COPY ./debug/error_reporting.ini /usr/local/etc/php/conf.d/error_reporting.ini

# Install nodemon to watch php server
RUN apt-get update \
    && apt-get install -y nodejs npm \
    && rm -rf /var/lib/apt/lists/* /var/cache/apt/archives/*

RUN npm install -g nodemon

ENTRYPOINT nodemon --exec php "$INDEX_FILE" --watch . --legacy-watch -e php "$INDEX_FILE"


################## Production image ##################
FROM base AS production
ENV INDEX_FILE="react/index.php"

COPY ./src /var/www/html/src
COPY ./react/index.php /var/www/html/react/index.php

ENTRYPOINT ["php", "$INDEX_FILE"]
