FROM php:8.2-apache

# Install PostgreSQL client library dependencies and PDO extension
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Enable Apache rewrite module for routing
RUN a2enmod rewrite

# Set working directory to Apache root
WORKDIR /var/www/html

# Copy backend files to Apache web directory
COPY . /var/www/html/

# Configure Apache DocumentRoot to point to public/
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/0000-default.conf

# Set environment port binding for Render
ENV PORT 8080
EXPOSE 8080

# Configure Apache to listen on Render's dynamic port
RUN sed -i 's/80/${PORT}/g' /etc/apache2/ports.conf /etc/apache2/sites-available/*.conf

CMD ["apache2-foreground"]