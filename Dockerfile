# PHP 8.2 with Apache
FROM php:8.2-apache

# Install MySQL extensions
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Enable Apache mod_rewrite (useful for frameworks/pretty URLs)
RUN a2enmod rewrite

# Copy app (if your code lives in /demo2, change COPY line accordingly)
WORKDIR /var/www/html
COPY demo2/ /var/www/html
# For code in /demo2 instead:
# COPY demo2/ /var/www/html

EXPOSE 80
