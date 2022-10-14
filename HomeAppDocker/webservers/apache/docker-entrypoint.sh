#!/bin/sh

echo "Setting server name to ${APP_NAME}"
echo "ServerName ${APP_NAME}" >> /etc/apache2/sites-enabled/000-default.conf

if [ "${1#-}" != "$1" ]; then
	set -- apache2-foreground "$@"
fi

if [ ! -f /etc/logs/server-error.log ]; then
    touch /etc/logs/server-error.log
fi
if [ ! -f /etc/logs/user-input-errors.log ]; then
    touch /etc/logs/user-input-error.log
fi

exec /usr/local/bin/docker-php-entrypoint "$@"
