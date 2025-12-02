# Use official PHP + Apache image
FROM php:8.2-apache

# Enable mod_rewrite
RUN a2enmod rewrite

# Install mysqli extension (for MySQL)
RUN docker-php-ext-install mysqli

# Copy project files into Apache document root
COPY . /var/www/html/

# Set working directory
WORKDIR /var/www/html/

# Ensure permissions
RUN chown -R www-data:www-data /var/www/html
