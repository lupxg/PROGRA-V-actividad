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

RUN a2enmod rewrite \
 && docker-php-ext-install mysqli opcache \
 && sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf \
 && echo "DirectoryIndex routex.php" > /etc/apache2/conf-available/routes.conf \
 && a2enconf routes \
 && mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

COPY infrastructure/apache/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY --from=deps app/vendor/ /var/www/html/vendor
COPY --chown=www-data:www-data ./ApiBiblioteca /var/www/html

