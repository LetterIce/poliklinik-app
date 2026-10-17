# ==========================================
# 1. Build Frontend Assets (Vite & Tailwind)
# ==========================================
FROM node:20-alpine AS assets_builder

WORKDIR /app

# Copy dependency definitions
COPY package*.json ./
RUN npm install

# Copy application files needed for Vite build & Tailwind scanning
COPY vite.config.js ./
COPY resources ./resources
COPY public ./public

RUN npm run build

# ==========================================
# 2. PHP-FPM Application Environment
# ==========================================
FROM php:8.3-fpm-bookworm

# Set working directory
WORKDIR /var/www

# Install system utilities and PHP extension installer
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    curl \
    unzip \
    libcap2-bin \
    default-mysql-client \
    && rm -rf /var/lib/apt/lists/*

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions \
        pdo_mysql \
        bcmath \
        gd \
        zip \
        pcntl \
        redis \
        opcache \
        intl

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy custom PHP configuration
COPY docker/php/custom.ini /usr/local/etc/php/conf.d/custom.ini

# Copy entrypoint script
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Copy project code
COPY . /var/www

# Copy compiled frontend assets from assets_builder
COPY --from=assets_builder /app/public/build /var/www/public/build

# Create backup of public assets to sync into persistent volumes upon startup
RUN cp -r /var/www/public /var/www/public_template

# Install production PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# Set file permissions for Laravel
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache && \
    chmod -R 775 /var/www/storage /var/www/bootstrap/cache

EXPOSE 9000

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm"]
