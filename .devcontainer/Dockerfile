FROM php:8.2-apache

RUN apt-get update && apt-get install -y libzip-dev unzip git \
    && docker-php-ext-install zip pdo pdo_mysql

RUN a2enmod rewrite

COPY apache-site.conf /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html

RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html
