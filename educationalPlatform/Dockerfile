FROM node:22-alpine AS assets

WORKDIR /build
COPY package.json package-lock.json ./
RUN npm ci
COPY resources ./resources
COPY vite.config.js ./
RUN npm run build

FROM php:8.3.28-fpm-alpine

RUN apk add --no-cache bash nginx supervisor libpq libzip icu-libs \
    && apk add --no-cache --virtual .build-deps $PHPIZE_DEPS postgresql-dev libzip-dev icu-dev \
    && docker-php-ext-install pdo_pgsql pdo_mysql intl zip opcache \
    && apk del .build-deps

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini

COPY docker/php/docker-vars.ini /usr/local/etc/php/conf.d/docker-vars.ini
COPY docker/php/www-overrides.conf /usr/local/etc/php-fpm.d/zz-overrides.conf
COPY docker/nginx/nginx-site.conf /etc/nginx/http.d/default.conf
COPY docker/supervisor/supervisord.conf /etc/supervisord.conf
COPY docker/start.sh /start.sh

WORKDIR /var/www/html
COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction \
    && chown -R www-data:www-data storage bootstrap/cache

COPY --from=assets /build/public/build ./public/build

ENV APP_ENV=production
ENV APP_DEBUG=false
ENV LOG_CHANNEL=stderr
ENV COMPOSER_ALLOW_SUPERUSER=1

EXPOSE 80
CMD ["/start.sh"]
