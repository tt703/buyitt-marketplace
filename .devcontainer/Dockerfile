# Use official PHP + Apache image
FROM php:8.2-apache

# Copy the virtual host configuration file
COPY ./my-site.conf /etc/apache2/sites-available/my-site.conf

# Enable the virtual host
RUN a2ensite my-site.conf

# Disable the default site (optional)
RUN a2dissite 000-default.conf

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    git \
    && docker-php-ext-install zip

# Install required PHP extensions
RUN docker-php-ext-install pdo pdo_mysql

# Enable Apache modules
RUN a2enmod rewrite

# Set the working directory
WORKDIR /var/www/html

# Set permissions
RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html