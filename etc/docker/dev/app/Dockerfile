FROM webplates/symfony-php-dev:7.0-fpm

ARG USE_XDEBUG=no

RUN set -ex \
    && if [ $USE_XDEBUG = "yes" ]; then \
        pecl install xdebug && docker-php-ext-enable xdebug; \
        echo "xdebug.remote_enable=on" >> /usr/local/etc/php/conf.d/xdebug.ini; \
        echo "xdebug.remote_autostart=1" >> /usr/local/etc/php/conf.d/xdebug.ini; \
        echo "xdebug.remote_connect_back=off" >> /usr/local/etc/php/conf.d/xdebug.ini; \
    fi

RUN set -xe \
    && apt-get update \
    && apt-get install -y libxml2-dev \
    && docker-php-ext-install -j$(nproc) soap

COPY php.ini /usr/local/etc/php/conf.d/custom.ini
COPY entrypoint.sh /docker-entrypoint.sh

ENTRYPOINT ["/docker-entrypoint.sh"]
