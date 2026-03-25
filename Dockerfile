# Stage 1: Frontend & Blockchain Builder
FROM mirror.gcr.io/library/node:18-alpine AS builder
WORKDIR /app

# Install build tools for native npm modules
RUN apk add --no-cache build-base git python3

# 1. Copy ALL manifest files first (Root, Admin, Shop, Blockchain)
COPY package.json package-lock.json* ./
COPY packages/Webkul/Admin/package.json packages/Webkul/Admin/package.json
COPY packages/Webkul/Shop/package.json packages/Webkul/Shop/package.json
COPY blockchain/package.json blockchain/package-lock.json* blockchain/

# 2. Cache NPM packages and install dependencies in parallel/layers
RUN --mount=type=cache,target=/root/.npm \
    npm i --legacy-peer-deps && \
    npm --prefix packages/Webkul/Admin i --legacy-peer-deps && \
    npm --prefix packages/Webkul/Shop i --legacy-peer-deps && \
    cd blockchain && npm i --ignore-engines

# 3. Copy ALL source code needed for builds
COPY . .

# 4. Run ALL builds (Root, Admin, Shop, Blockchain)
# Admin build was missing in previous version!
RUN npm run build && \
    npm --prefix packages/Webkul/Admin run build && \
    npm --prefix packages/Webkul/Shop run build && \
    cd blockchain && npx hardhat compile


# Stage 2: Final PHP Application
FROM mirror.gcr.io/library/php:8.3-fpm

# Use cache for apt to speed up rebuilds and switch to a faster debian mirror
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

# Install PHP extensions
ENV IPE_GD_WITHOUTAVIF=1
RUN install-php-extensions pdo_mysql exif pcntl bcmath calendar redis soap gd intl gmp zip

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# 1. Copy composer manifests and packages (needed for path repos)
COPY composer.json composer.lock ./
COPY packages/ packages/

# 2. Install PHP dependencies with Composer Cache
RUN --mount=type=cache,target=/root/.composer/cache \
    composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# 3. Copy the rest of the application
COPY . .

# 4. Copy built assets and blockchain artifacts from builder stage
# This ensures we don't need nodejs in this final image
COPY --from=builder /app/public/build /var/www/html/public/build
COPY --from=builder /app/public/themes /var/www/html/public/themes
COPY --from=builder /app/blockchain/artifacts /var/www/html/blockchain/artifacts
COPY --from=builder /app/blockchain/cache /var/www/html/blockchain/cache

# Finalize autoloader and run post-install scripts
RUN composer dump-autoload --optimize --no-dev

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/log/nginx

# Copy and configure server
COPY docker/nginx.conf /etc/nginx/sites-available/default
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/php.ini /usr/local/etc/php/conf.d/custom.ini
COPY docker/zz-docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf
COPY start-container.sh /usr/local/bin/start-container
COPY fix-deployment.php /var/www/html/fix-deployment.php
RUN chmod +x /usr/local/bin/start-container

EXPOSE 80
ENTRYPOINT ["start-container"]
