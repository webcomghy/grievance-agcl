FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    cron \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    unzip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Set working directory
WORKDIR /var/www/html/grievance-redressal

# Copy existing application directory contents
COPY . .

# Get Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install dependencies
RUN composer install

# Change ownership of our applications
RUN chown -R www-data:www-data /var/www/html/grievance-redressal

# Change permissions of the storage and bootstrap/cache directories
RUN chown -R www-data:www-data /var/www/html/grievance-redressal/bootstrap/cache
RUN chmod -R 755 /var/www/html/grievance-redressal/bootstrap/cache
RUN chmod -R 755 /var/www/html/grievance-redressal/storage

# Enable SSL and other Apache modules
RUN a2enmod ssl
COPY apache/default-ssl.conf /etc/apache2/sites-available/default-ssl.conf
RUN a2ensite default-ssl
COPY ssl/assamgas.co.in.key /etc/ssl/assamgas.co.in.key
COPY ssl/b81ee2f8fab4935d.crt /etc/ssl/b81ee2f8fab4935d.crt
COPY ssl/gd_bundle-g2-g1.crt /etc/ssl/gd_bundle-g2-g1.crt

# Set permissions for SSL files
RUN chmod 600 /etc/ssl/assamgas.co.in.key
RUN chmod 644 /etc/ssl/b81ee2f8fab4935d.crt /etc/ssl/gd_bundle-g2-g1.crt

# Enable mod_rewrite
RUN a2enmod rewrite

# Configure cron job
COPY ./cron/my-cron /etc/cron.d/my-cron
RUN chmod 0644 /etc/cron.d/my-cron
RUN crontab /etc/cron.d/my-cron

# Expose port 443
EXPOSE 443

# Start cron and Apache
CMD service cron start && apache2-foreground
