# Backend Dockerfile for Symfony Application
FROM php:8.4-cli-alpine

# Install system dependencies
RUN apk add --no-cache \
    git \
    unzip \
    libzip-dev \
    postgresql-dev \
    icu-dev \
    oniguruma-dev \
    && docker-php-ext-install \
    pdo \
    pdo_pgsql \
    zip \
    intl \
    mbstring

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files
COPY composer.json composer.lock ./

# Install PHP dependencies
RUN composer install --no-scripts --no-autoloader --prefer-dist

# Copy application files
COPY . .

# Generate autoloader
RUN composer dump-autoload --optimize

# Create necessary directories
RUN mkdir -p var/cache var/log config/jwt && \
    chmod -R 777 var/cache var/log config/jwt

# Expose port
EXPOSE 8000

# Default command (overridden in docker-compose.yml)
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public/"]
