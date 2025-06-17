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

# Set ServerName to avoid warning
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Set working directory
WORKDIR /var/www/html

# Copy app code into container
COPY ./src /var/www/html/



# Set permissions
RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html

# Expose port
EXPOSE 80
# Enable Apache rewrite and set permissions properly
RUN a2enmod rewrite

# Add this Apache config snippet to allow overrides and permissions
RUN echo '<Directory /var/www/html>' >> /etc/apache2/sites-available/000-default.conf \
 && echo '    Options Indexes FollowSymLinks' >> /etc/apache2/sites-available/000-default.conf \
 && echo '    AllowOverride All' >> /etc/apache2/sites-available/000-default.conf \
 && echo '    Require all granted' >> /etc/apache2/sites-available/000-default.conf \
 && echo '</Directory>' >> /etc/apache2/sites-available/000-default.conf
