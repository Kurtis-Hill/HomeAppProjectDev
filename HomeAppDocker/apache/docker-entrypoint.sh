#!/bin/bash
set -e
#dont think i need this anymore
sed -E '/xdebug\.remote_host=.+/d' /usr/local/etc/php/conf.d/xdebug.ini > /usr/local/etc/php/conf.d/xdebug.ini.tmp && mv /usr/local/etc/php/conf.d/xdebug.ini.tmp /usr/local/etc/php/conf.d/xdebug.ini

sleep 5

if [ "${1#-}" != "$1" ]; then
	set -- apache2-foreground "$@"
fi

if [ ${APP_ENV} == 'prod' ]; then
	echo "production container build"
	echo "checking connection to github"
	if ping -c 1 api.github.com &> /dev/null 
	then
		echo "git hub connection made"
		echo "Installing composer packages..."
		php -d memory_limit=-1 `which composer` install --prefer-dist --no-interaction --no-dev
		echo "...Composer packages installed"
	else
		echo "No internet connection"
	fi
	
	if [ -f /usr/local/etc/php/conf.d/xdebug.ini ]; then
		echo "Removing xdebug config"
    	rm -r /usr/local/etc/php/conf.d/xdebug.ini
	fi

	echo "Executing database migrations production..."
	bin/console d:m:m --no-interaction
	echo "...Migrations complete"
fi


if [ ${APP_ENV} == 'dev' ]; then
	sleep 5
	echo "dev container build"
	echo "Executing database migrations dev enviromennt..."
	bin/console d:m:m --no-interaction --env=test
	echo "...Migrations complete"

	echo "Querying test database"
	 if php bin/console dbal:run-sql "select firstName from user where firstName = 'admin' limit 1" --env=test | grep -q 'array(0)'; then
		echo "Test database empty loading fixtures..."
   			 php bin/console doctrine:fixtures:load --no-interaction --env=test
    echo "...Fixtures loaded"
	fi
	echo "Test database checked"
fi

if [ ! -f /etc/logs/server-errors.log ]; then
    touch /etc/logs/server-errors.log
fi

if [ ! -f /etc/logs/user-input-errors.log ]; then
    touch /etc/logs/user-input-errors.log
fi

exec /usr/local/bin/docker-php-entrypoint "$@"
