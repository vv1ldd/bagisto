# Stage 1: Frontend Build
FROM node:18-alpine AS frontend
WORKDIR /app
COPY package.json package-lock.json* ./
# Cache NPM packages and install
RUN --mount=type=cache,target=/root/.npm \
    npm i || true
COPY . .
RUN npm run build

# Stage 2: PHP Application
FROM php:8.3-fpm

# Use cache for apt to speed up rebuilds and switch to a faster debian mirror
RUN --mount=type=cache,target=/var/cache/apt,sharing=locked \
    --mount=type=cache,target=/var/lib/apt,sharing=locked \
    sed -i 's/deb.debian.org/mirror.yandex.ru/g' /etc/apt/sources.list.d/debian.sources || true && \
    apt-get update && apt-get install -y --no-install-recommends \
    git \
    curl \
    zip \
    unzip \
    supervisor \
    nginx \
    && curl -sSLf \
    -o /usr/local/bin/install-php-extensions \
    https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions \
    && chmod +x /usr/local/bin/install-php-extensions \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions in two batches to avoid timeouts/OOM
ENV IPE_GD_WITHOUTAVIF=1
RUN install-php-extensions \
    pdo_mysql \
    exif \
    pcntl \
    bcmath \
    calendar \
    redis \
    soap

RUN install-php-extensions \
    gd \
    intl \
    gmp \
    zip

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files and packages first (needed for path repositories)
COPY composer.json composer.lock ./
COPY packages/ packages/

# Install PHP dependencies with Composer Cache
RUN --mount=type=cache,target=/root/.composer/cache \
    composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Copy the rest of the application
COPY . .

# Copy built assets from frontend stage
COPY --from=frontend /app/public/build /var/www/html/public/build
COPY --from=frontend /app/public/themes /var/www/html/public/themes

# Finalize autoloader and run post-install scripts
RUN composer dump-autoload --optimize --no-dev

# Copy Nginx and Supervisor configurations
COPY docker/nginx.conf /etc/nginx/sites-available/default
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/php.ini /usr/local/etc/php/conf.d/custom.ini
COPY docker/zz-docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/log/nginx

# Copy start script
COPY start-container.sh /usr/local/bin/start-container
COPY fix-deployment.php /var/www/html/fix-deployment.php
RUN chmod +x /usr/local/bin/start-container

EXPOSE 8000

ENTRYPOINT ["start-container"]
