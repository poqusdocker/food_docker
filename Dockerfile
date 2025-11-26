FROM php:8.2-fpm

# Install PHP extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Set working directory
WORKDIR /var/www/html

# Copy source code
COPY . /var/www/html/

# Fix permissions
RUN chown -R www-data:www-data /var/www/html
