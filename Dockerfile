FROM php:8.2-fpm

# Install PHP extensions needed for your app (e.g., mysqli, pdo, pdo_mysql)
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copy application code to container
COPY . /var/www/html/
