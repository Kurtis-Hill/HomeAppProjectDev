FROM sebp/lighttpd

WORKDIR /var/www/html

RUN apt-get update -y && apt-get install -y \
git \
unzip \
zip \
supervisor

RUN apt-get install apt-transport-https -y 

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions gd pdo_mysql bcmath zip intl opcache pcntl-7.4 sockets http apcu

COPY ./auth/auth.json /root/.composer/auth.json

COPY ./supervisor/ /etc/supervisor/conf.d/

COPY ./lighttpd/docker-entrypoint.sh /usr/bin/docker-entrypoint
RUN chmod +x /usr/bin/docker-entrypoint

ENTRYPOINT ["docker-entrypoint", "-D", "FOREGROUND"]