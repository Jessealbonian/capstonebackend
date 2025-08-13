# Use PHP with Apache
FROM php:8.2-apache

# Enable mysqli and pdo_mysql extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copy your backend files from the right folder to Apache's web root
COPY demo2/demoproject/ /var/www/html/

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Optional: enable Apache rewrite if you have pretty URLs
RUN a2enmod rewrite
