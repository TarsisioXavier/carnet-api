FROM php:8.3-fpm-alpine

RUN adduser app -shell /bin/sh --disabled-password --uid 1000

# Configuring the correct timezone
# --------------------------------------------------------------------------------
RUN apk add --no-cache --update tzdata
RUN ln -s /usr/share/zoneinfo/America/Sao_Paulo /etc/localtime

# Using binaries from another images
# --------------------------------------------------------------------------------
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

# Installing PHP dependencies
# --------------------------------------------------------------------------------
RUN apk --no-cache --update add git php83-pecl-redis libmemcached-dev zlib-dev \
    libpng-dev libjpeg-turbo-dev freetype-dev libxml2-dev

RUN echo "extension=redis.so" >> /etc/php83/php.ini

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd fileinfo dom xml simplexml pcntl mysqli pdo_mysql

RUN IPE_GD_WITHOUTAVIF=1 install-php-extensions redis-stable

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

# Debugging timezone
RUN date

# Debugging PHP
RUN php -v && php -i
