FROM php:8.3-apache

RUN a2enmod rewrite

ADD . /var/www/html

RUN docker-php-ext-install pdo pdo_mysql