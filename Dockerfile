# Use PHP with Apache
FROM php:8.2-apache

# Enable mysqli and pdo_mysql extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Raise upload limits for routine image submissions
RUN { \
    echo "file_uploads=On"; \
    echo "upload_max_filesize=10M"; \
    echo "post_max_size=12M"; \
    echo "max_file_uploads=20"; \
    echo "memory_limit=256M"; \
  } > /usr/local/etc/php/conf.d/uploads.ini

# Copy your backend files from the right folder to Apache's web root
COPY demo2/demoproject/ /var/www/html/

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Optional: enable Apache rewrite if you have pretty URLs
RUN a2enmod rewrite
