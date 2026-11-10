FROM php:8.4-cli-trixie

WORKDIR /var/www/html

RUN apt-get update -y \
    && apt-get upgrade -y \
    && apt-get install -y --no-install-recommends \
        git \
        unzip \
        zip \
        supervisor \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

COPY --from=mlocati/php-extension-installer:2 /usr/bin/install-php-extensions /usr/local/bin/

RUN install-php-extensions gd pdo_mysql bcmath zip intl opcache sockets http apcu

COPY ./auth/auth.json /root/.composer/auth.json

COPY ./supervisor/ /etc/supervisor/conf.d/

COPY ./php-cli/docker-entrypoint.sh /usr/bin/docker-entrypoint
RUN chmod +x /usr/bin/docker-entrypoint

ENTRYPOINT ["docker-entrypoint"]

CMD ["php"]
