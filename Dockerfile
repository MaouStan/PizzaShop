# Use an official PHP runtime as a parent image
FROM php:8.2-apache

# Set the working directory in the container
WORKDIR /var/www/html

# Install any dependencies your PHP application needs
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql gd mysqli

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy the current directory contents into the container
COPY . /var/www/html

# Copy .env file
COPY .env /var/www/html/.env

# Install phpdotenv using Composer
RUN composer require vlucas/phpdotenv

# Load environment variables
ENV $(cat /var/www/html/.env | xargs)

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod 644 /var/www/html/.env \
    && chmod +x /var/www/html/start.sh

# Add this line to set ServerName
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Expose port 80
EXPOSE 80

# Start Apache when the container launches
CMD ["/var/www/html/start.sh"]
