FROM php:7.4-apache

RUN apt-get update && apt-get install -y

RUN apt-get update && apt-get install -y --no-install-recommends \
        git \
        zlib1g-dev \
        libxml2-dev \
        libzip-dev \
    && docker-php-ext-install \
        zip \
        intl \
        mysqli \
        pdo pdo_mysql \
    && a2enmod rewrite \
    && service apache2 restart

WORKDIR /var/www/html 
COPY --chown=33:33 ./ /var/www/html 

RUN chown -R 33:33 /var/www/html 