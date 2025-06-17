# Use official PHP + Apache image
FROM php:8.2-apache

# Install dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev unzip git curl \
    libpng-dev libjpeg-dev libfreetype6-dev \
    libonig-dev libxml2-dev libpq-dev

# Install PHP extensions
RUN docker-php-ext-install zip pdo pdo_mysql gd mbstring xml

# Enable Apache rewrite module
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy public files directly into Apache web root
COPY ./public/ /var/www/html/

# Set permissions
RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html

# Expose port
EXPOSE 80
