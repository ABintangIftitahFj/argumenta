FROM node:24-alpine AS frontend

WORKDIR /var/www/html
COPY package.json package-lock.json ./
RUN npm ci
COPY vite.config.ts tsconfig.json* ./
COPY resources ./resources
RUN npm run build

FROM composer:2 AS php-dependencies

WORKDIR /var/www/html
COPY composer.json ./
COPY app ./app
COPY bootstrap ./bootstrap
COPY config ./config
COPY database ./database
COPY routes ./routes
COPY artisan ./
RUN composer install --no-interaction --prefer-dist --no-progress --optimize-autoloader

FROM php:8.3-cli-alpine

WORKDIR /var/www/html

RUN apk add --no-cache \
        icu-dev \
        oniguruma-dev \
        libzip-dev \
    && docker-php-ext-install \
        intl \
        mbstring \
        pdo_mysql \
        zip

COPY . .
COPY --from=php-dependencies /var/www/html/vendor ./vendor
COPY --from=frontend /var/www/html/public/build ./public/build

RUN cp -n .env.example .env || true
RUN chmod -R 777 storage bootstrap/cache

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
