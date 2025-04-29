FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    git \
    libpq-dev \
    postgis \
    postgresql-15-postgis-3 \
    netcat-openbsd \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql pdo_pgsql opcache

# Enable Apache rewrite module
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy composer from composer image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy Laravel source
COPY . .

# Copy custom php.ini (contains opcache settings)
COPY docker/php.ini /usr/local/etc/php/conf.d/custom.ini

# Copy .env (ensure it exists)
COPY .env .env

# Install Composer dependencies
RUN composer install --optimize-autoloader --no-dev

# Cache Laravel configs, routes, views
RUN php artisan config:cache \
 && php artisan route:cache \
 && php artisan view:cache \
 && php artisan key:generate

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
 && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 80
ENTRYPOINT ["/entrypoint.sh"]