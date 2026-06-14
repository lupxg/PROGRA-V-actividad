# syntax=docker/dockerfile:1


################################################################################

FROM composer:2.8 AS deps

WORKDIR /app

RUN --mount=type=bind,source=./ApiBiblioteca/composer.json,target=composer.json \
    --mount=type=bind,source=./ApiBiblioteca/composer.lock,target=composer.lock \
    --mount=type=cache,target=/tmp/composer-cache \
    COMPOSER_CACHE_DIR=/tmp/composer-cache \
    composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader


################################################################################

FROM php:8.3-apache AS final

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        curl \
    && docker-php-ext-install \
        mysqli \
        opcache \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/* \
    && mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    && { \
        echo 'opcache.enable=1'; \
        echo 'opcache.memory_consumption=128'; \
        echo 'opcache.interned_strings_buffer=8'; \
        echo 'opcache.max_accelerated_files=4000'; \
        echo 'opcache.revalidate_freq=2'; \
        echo 'opcache.fast_shutdown=1'; \
    } > /usr/local/etc/php/conf.d/opcache.ini

# Loading Apache config
COPY --chown=www-data:www-data ./infrastructure/apache/000-default.conf /etc/apache2/sites-enabled/000-default.conf
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf \ 
    && a2ensite 000-default.conf

WORKDIR /var/www/html

COPY --chown=www-data:www-data --from=deps app/vendor/ /var/www/html/vendor
COPY --chown=www-data:www-data ./ApiBiblioteca/composer.json /var/www/html/composer.json
COPY --chown=www-data:www-data ./frontend /var/www/html/frontend
COPY --chown=www-data:www-data ./ApiBiblioteca /var/www/html/ApiBiblioteca

EXPOSE 80

CMD ["apache2-foreground"]
