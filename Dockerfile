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
# SECURITY: CVE-2026-109618 / CVE-2026-109619 (CVSS 4.0 9.2 CRITICAL)
#   RCE via OOM corruption and stale global state in the PHP core engine.
#   Patched in PHP 8.4.27 / 8.3.36 (released 2026-10-21).
#   The official docker.io/library/php images did not publish an arm64
#   manifest for 8.4.27 yet, so PHP comes from the Debian trixie-security
#   repository, which ships php8.4 8.4.27-1~deb13u1 for arm64.
FROM debian:trixie

# Set working directory
WORKDIR /var/www

# Install system utilities and the patched PHP 8.4.27 stack
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    curl \
    unzip \
    libcap2-bin \
    default-mysql-client \
    ca-certificates \
    php8.4-fpm \
    php8.4-cli \
    php8.4-mysql \
    php8.4-bcmath \
    php8.4-gd \
    php8.4-zip \
    php8.4-opcache \
    php8.4-intl \
    php8.4-curl \
    php8.4-mbstring \
    php8.4-xml \
    php8.4-redis \
    && rm -rf /var/lib/apt/lists/* \
    && update-alternatives --set php /usr/bin/php8.4 \
    && update-alternatives --set phar /usr/bin/phar8.4 \
    && update-alternatives --set phar.phar /usr/bin/phar.phar8.4

# php-fpm needs its config directory in place
RUN mkdir -p /etc/php/8.4/fpm/pool.d /etc/php/8.4/fpm/conf.d \
    && mkdir -p /run/php \
    && chown www-data:www-data /run/php

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy custom PHP configuration
COPY docker/php/custom.ini /etc/php/8.4/fpm/conf.d/custom.ini
COPY docker/php/fpm-override.conf /etc/php/8.4/fpm/pool.d/zz-override.conf

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
CMD ["php-fpm8.4", "-F"]
