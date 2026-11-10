FROM php:8.4-apache-trixie

ARG APP_ENV=prod

WORKDIR /var/www/html

RUN apt-get update -y \
    && apt-get upgrade -y \
    && apt-get install -y --no-install-recommends \
        ca-certificates \
    && update-ca-certificates \
    && rm -rf /var/lib/apt/lists/*

COPY --from=mlocati/php-extension-installer:2 /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions gd pdo_mysql bcmath zip intl opcache sockets http apcu

# Xdebug is a debugging tool and should never be present in production images
COPY ./apache/conf.d/xdebug.ini /tmp/xdebug.ini
RUN if [ "${APP_ENV}" = "dev" ]; then \
        install-php-extensions xdebug \
        && rm -f /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
        && mv /tmp/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini; \
    else \
        rm -f /tmp/xdebug.ini; \
    fi

COPY ./auth/auth.json /root/.composer/auth.json

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

COPY ./apache/sites-enabled/site.conf /etc/apache2/sites-available/000-default.conf

COPY ./apache/php-config/php.ini /usr/local/etc/php/conf.d/extra-php-config.ini

COPY ./apache/docker-entrypoint.sh /usr/bin/docker-entrypoint
RUN chmod +x /usr/bin/docker-entrypoint

RUN a2enmod rewrite ssl remoteip

RUN mkdir -p /var/www/html/assets/images/profile-pics /var/www/html/tests/test-files \
    && chown www-data /var/www/html/assets/images/profile-pics /var/www/html/tests/test-files

ENTRYPOINT ["docker-entrypoint", "-D", "FOREGROUND"]
