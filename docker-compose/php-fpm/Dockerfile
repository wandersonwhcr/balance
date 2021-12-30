ARG PHP_VERSION=5.6

FROM php:${PHP_VERSION}-fpm-alpine

ENV COMPOSER_CACHE_DIR /tmp

COPY --from=composer:2.2 /usr/bin/composer /usr/local/bin/composer

RUN apk add --no-cache fcgi unzip \
    && echo "pm.status_path = /status" >> /usr/local/etc/php-fpm.d/zz-docker.conf \
    && curl --silent --remote-name https://raw.githubusercontent.com/renatomefi/php-fpm-healthcheck/v0.6.0/php-fpm-healthcheck \
    && install -o root -g root -m 755 php-fpm-healthcheck /usr/local/bin/php-fpm-healthcheck \
    && rm -rf php-fpm-healthcheck

HEALTHCHECK --interval=5s \
    CMD php-fpm-healthcheck || exit 1

RUN apk add --no-cache $PHPIZE_DEPS \
    && apk add --no-cache icu-dev postgresql-dev \
    && docker-php-ext-install bcmath intl pgsql \
    && pecl install 'xdebug-2.5.5' \
    && docker-php-ext-enable xdebug \
    && apk add --no-cache git npm \
    && npm install --global bower
