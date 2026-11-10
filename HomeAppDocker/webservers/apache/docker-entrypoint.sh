#!/bin/sh

echo "Setting server name to ${APP_NAME}"
echo "ServerName ${APP_NAME}" >> /etc/apache2/sites-enabled/000-default.conf

if [ "${1#-}" != "$1" ]; then
	set -- apache2-foreground "$@"
fi

exec /usr/local/bin/docker-php-entrypoint "$@"
