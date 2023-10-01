FROM php:8.2-fpm-alpine

WORKDIR /var/www/html

ENV COMPOSER_ALLOW_SUPERUSER=1 

RUN docker-php-ext-install pdo_mysql

#Composer

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

#Copy project files to the container's structure

COPY . .

#Install symfony dependencies

RUN composer install
