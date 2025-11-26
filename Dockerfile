FROM php:8.2-fpm

<<<<<<< HEAD
# Install PHP extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Set working directory
WORKDIR /var/www/html

# Copy source code
COPY . /var/www/html/

# Fix permissions (IMPORTANT)
RUN chown -R www-data:www-data /var/www/html
=======
# Install PHP extensions needed for your app (e.g., mysqli, pdo, pdo_mysql)
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copy application code to container
COPY . /var/www/html/
>>>>>>> 6329ee2e1f5c87978477b458ef69e5856366e0e5
