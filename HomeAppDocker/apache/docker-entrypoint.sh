#!/bin/sh

echo "Setting server name to ${APP_NAME}"
echo "ServerName ${APP_NAME}" >> /etc/apache2/sites-enabled/site.conf

if [ "${1#-}" != "$1" ]; then
	set -- apache2-foreground "$@"
fi

if [ ${APP_ENV} = 'prod' ]; then
  echo "installing composer packages..."
  composer install --no-dev --optimize-autoloader --prefer-dist --no-interaction
  echo "Executing database migrations production..."
  bin/console d:m:m --no-interaction
  echo "...Migrations complete"
fi


if [ ${APP_ENV} = 'dev' ]; then
  composer install --prefer-dist --no-interaction
	echo "dev container build"
	echo "Executing database migrations for test enviroment..."
	bin/console d:m:m --no-interaction --env=test
	echo "...Test migrations complete"
	echo "Executing database migrations for local enviroment..."

	bin/console doctrine:migrations:migrate -n
	echo "...Local migrations complete"

	echo "Querying test database"
	if php bin/console dbal:run-sql "select firstName from user where firstName = 'user' limit 1" --env=test | grep -q 'array(0)'; then
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

echo "Starting supervisor..."
supervisord -n&
echo "Supervisor Started..."

exec /usr/local/bin/docker-php-entrypoint "$@"
