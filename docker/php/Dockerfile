FROM php:8.1.2-fpm-alpine

#Install all Laravel dependencies
RUN apk add --update \
    $PHPIZE_DEPS \
    php7-mbstring \
    php7-session \
    php7-json \
    php7-pdo \
    php7-openssl \
    php7-tokenizer \
    php7-pdo \
    php7-pdo_mysql \
    php7-pdo_sqlite \
    php7-xml \
    php7-simplexml \
    sqlite-dev \
    && docker-php-ext-install pdo_mysql pdo_sqlite

COPY php.ini "$PHP_INI_DIR/php.ini"
