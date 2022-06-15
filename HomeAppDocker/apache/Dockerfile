FROM php:8.1-apache

WORKDIR /var/www/html

RUN mkdir /etc/logs

RUN apt-get update -y && apt-get install -y \
git \
unzip \
zip \
nano \
iputils-ping \
supervisor

RUN apt-get install apt-transport-https ca-certificates -y 

RUN update-ca-certificates

RUN pecl install xdebug

RUN a2enmod rewrite

RUN a2enmod ssl

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions gd pdo_mysql bcmath zip intl opcache pcntl-7.4 sockets http apcu

COPY ./conf.d/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

COPY ./auth.json /root/.composer/auth.json

COPY --from=composer:2.0 /usr/bin/composer /usr/local/bin/composer

COPY ./sites-enabled/site.conf /etc/apache2/sites-enabled/site.conf

COPY ./php-config/php.ini /usr/local/etc/php/conf.d/extra-php-config.ini

COPY ./supervisor/ /etc/supervisor/conf.d/

COPY ./docker-entrypoint.sh /usr/bin/docker-entrypoint
RUN chmod +x /usr/bin/docker-entrypoint

ENTRYPOINT ["docker-entrypoint", "-D", "FOREGROUND"]
