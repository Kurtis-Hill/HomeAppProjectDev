FROM php:8.0-apache

RUN a2enmod rewrite

RUN apt-get update && apt-get install -y git unzip zip nano iputils-ping

RUN pecl install xdebug

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions gd pdo_mysql bcmath zip intl opcache openssl

COPY ./conf.d/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

COPY --from=composer:2.0 /usr/bin/composer /usr/local/bin/composer

RUN chmod 0444 -R /etc/ssl

COPY ./docker-entrypoint.sh /usr/bin/docker-entrypoint
RUN chmod +x /usr/bin/docker-entrypoint

WORKDIR /var/www/html

ENTRYPOINT ["docker-entrypoint", "-D", "FOREGROUND"]