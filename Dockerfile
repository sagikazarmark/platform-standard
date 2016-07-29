FROM php:5.6-fpm-alpine

RUN set -xe \
    && apk add --no-cache \
        coreutils \
        freetype-dev \
        libjpeg-turbo-dev \
        libltdl \
        libmcrypt-dev \
        libpng-dev \
        icu icu-libs icu-dev \
        libxml2-dev \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) iconv mbstring mcrypt intl pdo_mysql gd soap

ENV COMPOSER_VERSION 1.1.0
ENV COMPOSER_ALLOW_SUPERUSER 1
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer --version=${COMPOSER_VERSION}

ENV SYMFONY_ENV prod

# Production PHP configuration
COPY etc/docker/prod/app/php.ini /usr/local/etc/php/php.ini

COPY composer.json composer.lock ./
RUN composer install --prefer-dist --no-dev --no-interaction --quiet --no-autoloader --no-scripts

COPY LICENSE .
COPY bin/ bin/
COPY app/ app/
COPY src/ src/
COPY var/ var/
COPY web/ web/

# Build and cleanup
RUN set -xe \
    && composer dump-autoload --optimize \
    && composer run-script post-install-cmd \
    && bin/console assetic:dump \
    && bin/console cache:clear --no-warmup \
    && rm -rf var/cache/* var/logs/*

VOLUME ["/var/www/html/public", "/var/www/html/var/attachment", "/var/www/html/var/sessions"]

COPY etc/docker/prod/app/entrypoint.sh /docker-entrypoint.sh

ENTRYPOINT ["/docker-entrypoint.sh"]
