FROM php:8.0-apache

RUN a2enmod rewrite

RUN mkdir /etc/logs

RUN apt-get update && apt-get install -y \
git \
unzip \
zip \
nano \
iputils-ping \
supervisor

RUN pecl install xdebug

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions gd pdo_mysql bcmath zip intl opcache pcntl-7.4 sockets http

COPY ./conf.d/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

COPY ./auth.json /root/.composer/auth.json

COPY --from=composer:2.0 /usr/bin/composer /usr/local/bin/composer

COPY ./sites-enabled/site.conf /etc/apache2/sites-enabled/site.conf

COPY ./php-config/php.ini /usr/local/etc/php/conf.d/extra-php-config.ini

RUN chmod 0444 -R /etc/ssl

COPY ./docker-entrypoint.sh /usr/bin/docker-entrypoint
RUN chmod +x /usr/bin/docker-entrypoint

RUN a2enmod ssl

WORKDIR /var/www/html

ENTRYPOINT ["docker-entrypoint", "-D", "FOREGROUND"]

CMD ["/usr/bin/supervisord"]
