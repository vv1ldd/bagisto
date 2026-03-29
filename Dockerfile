# STAGE 1: Shared Node Base
FROM mirror.gcr.io/library/node:18-bookworm-slim AS node-base
WORKDIR /app
RUN apt-get update && apt-get install -y --no-install-recommends build-essential git python3 && rm -rf /var/lib/apt/lists/*

# STAGE 2: Admin Assets Builder
FROM node-base AS admin-builder
COPY packages/Webkul/Admin/package.json packages/Webkul/Admin/package.json
RUN --mount=type=cache,target=/root/.npm \
    npm --prefix packages/Webkul/Admin i --legacy-peer-deps
COPY packages/Webkul/Admin/ packages/Webkul/Admin/
COPY vite.config.js ./
# Vite plugin needs public dir to exist
RUN mkdir -p public
RUN npm --prefix packages/Webkul/Admin run build

# STAGE 3: Shop Assets Builder
FROM node-base AS shop-builder
COPY packages/Webkul/Shop/package.json packages/Webkul/Shop/package.json
RUN --mount=type=cache,target=/root/.npm \
    npm --prefix packages/Webkul/Shop i --legacy-peer-deps
COPY packages/Webkul/Shop/ packages/Webkul/Shop/
COPY vite.config.js ./
RUN mkdir -p public
RUN npm --prefix packages/Webkul/Shop run build

# STAGE 4: Root Assets Builder
FROM node-base AS root-builder
COPY package.json package-lock.json* ./
RUN --mount=type=cache,target=/root/.npm \
    npm i --legacy-peer-deps
COPY . .
RUN npm run build

# STAGE 5: Final PHP Application
FROM mirror.gcr.io/library/php:8.3-fpm

# System Dependencies & PHP Tools
RUN --mount=type=cache,target=/var/cache/apt,sharing=locked \
    --mount=type=cache,target=/var/lib/apt,sharing=locked \
    sed -i 's/deb.debian.org/mirror.yandex.ru/g' /etc/apt/sources.list.d/debian.sources || true && \
    apt-get update && apt-get install -y --no-install-recommends \
    git curl zip unzip supervisor nginx \
    && curl -sSLf \
    -o /usr/local/bin/install-php-extensions \
    https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions \
    && chmod +x /usr/local/bin/install-php-extensions \
    && rm -rf /var/lib/apt/lists/*

# PHP Extensions
ENV IPE_GD_WITHOUTAVIF=1
RUN install-php-extensions pdo_mysql exif pcntl bcmath calendar soap gd intl gmp zip \
    && install-php-extensions igbinary msgpack redis

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Composer Dependencies (Crawl packages first for path repos)
COPY composer.json composer.lock ./
COPY packages/ packages/
RUN --mount=type=cache,target=/root/.composer/cache \
    composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Copy Application Code
COPY . .

# Copy Built Assets from Parallel Builders
COPY --from=admin-builder /app/public/themes/admin /var/www/html/public/themes/admin
COPY --from=shop-builder /app/public/themes/shop /var/www/html/public/themes/shop
# Root assets (if any) built by root-builder
COPY --from=root-builder /app/public/build /var/www/html/public/build

# Finalize PHP & Permissions
RUN composer dump-autoload --optimize --no-dev \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/log/nginx

# Configuration
COPY docker/nginx.conf /etc/nginx/sites-available/default
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/php.ini /usr/local/etc/php/conf.d/custom.ini
COPY docker/zz-docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf
COPY start-container.sh /usr/local/bin/start-container
COPY fix-deployment.php /var/www/html/fix-deployment.php
RUN chmod +x /usr/local/bin/start-container

EXPOSE 80
ENTRYPOINT ["start-container"]
