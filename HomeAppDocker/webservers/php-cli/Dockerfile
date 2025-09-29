FROM php:8.2-cli-bullseye

WORKDIR /var/www/html

RUN apt-get update -y && apt-get install -y \
git \
unzip \
zip \
supervisor

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# if no network connection and compose is installed locally, use this instead:
#COPY --from=composer:2.0 /usr/bin/composer /usr/local/bin/composer
#COPY --from=composer:2.0 /usr/bin/composer /usr/local/bin/composer

#RUN composer --version

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN install-php-extensions gd pdo_mysql bcmath zip intl opcache sockets http apcu

COPY ./auth/auth.json /root/.composer/auth.json

COPY ./supervisor/ /etc/supervisor/conf.d/

COPY ./php-cli/docker-entrypoint.sh /usr/bin/docker-entrypoint
RUN chmod +x /usr/bin/docker-entrypoint

ENTRYPOINT ["docker-entrypoint"]

CMD ["php"]
