FROM php:8.2-fpm AS base

WORKDIR /var/www/html

# Install dependencies
RUN apt-get update \
    && apt-get install -y zip unzip libmagickwand-dev

RUN docker-php-ext-install mysqli pdo pdo_mysql

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER=1


################## Development image ##################
FROM base AS development

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# Should be put in the mutlistage dockerfile as dev base.
COPY ./debug/xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
COPY ./debug/error_reporting.ini /usr/local/etc/php/conf.d/error_reporting.ini

# Install nodemon to watch php server
RUN apt-get install -y \
    nodejs \
    npm
RUN npm install -g nodemon

EXPOSE 9000
EXPOSE 80

CMD ["nodemon", "--exec", "php", "./index.php", "--watch", ".", "--legacy-watch", "-e", "php", "./index.php"]


################## Production image ##################
FROM base AS production

COPY ../src /var/www/html/src
COPY ./index.php /var/www/html/index.php

EXPOSE 80

CMD ["php", "./index.php"]
