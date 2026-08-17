FROM php:8.2-apache

# Install PostgreSQL client dependencies and PDO extension
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Enable Apache mod_rewrite for REST routing
RUN a2enmod rewrite

# Set working directory to Apache root
WORKDIR /var/www/html

# Copy backend application files
COPY . /var/www/html/

# Update DocumentRoot to point to public/ (using correct filename: 000-default.conf)
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Configure Apache to listen on Render's dynamic $PORT environment variable
ENV PORT 8080
EXPOSE 8080

RUN sed -i 's/80/${PORT}/g' /etc/apache2/ports.conf /etc/apache2/sites-available/*.conf

CMD ["apache2-foreground"]