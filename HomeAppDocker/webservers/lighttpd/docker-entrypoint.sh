#!/bin/sh

echo "Setting server name to ${APP_NAME}"
echo "ServerName ${APP_NAME}" >> /etc/apache2/sites-enabled/000-default.conf

if [ "${1#-}" != "$1" ]; then
	set -- apache2-foreground "$@"
fi

if [ ${APP_ENV} = 'prod' ]; then
  echo "installing composer packages..."
  composer install --no-dev --optimize-autoloader --prefer-dist --no-interaction
  echo "Executing database migrations production..."
  bin/console doctrine:migrations:migrate -n
  echo "...Migrations complete"
fi

if [ ${APP_ENV} = 'dev' ]; then
	echo "dev container build"
	echo "Executing database migrations for test enviroment..."
	bin/console d:m:m --no-interaction --env=test
	echo "...Test migrations complete"

	echo "Executing database migrations for local enviroment..."
	bin/console doctrine:migrations:migrate -n
	echo "...Local migrations complete"

##@TODO: not working
	echo "Querying test database"
	if php bin/console dbal:run-sql "select firstName from user" --env=test | grep -q 'array(0)'; then
		echo "Test database empty loading fixtures..."
   		php bin/console doctrine:fixtures:load --no-interaction --env=test
    echo "...Fixtures loaded"
	fi
	echo "Test database checked"
fi

if [ ${ELASTIC_ENABLED} = 'true' ]; then
	echo "Elastic indicie creation"
	bin/console app:elastic-create-const-record-indices
	bin/console app:elastic-create-out-of-bounds-indices
	bin/console app:elastic-create-log-index
	echo "...Elastic indicie creation"
fi

echo "Starting supervisor..."
supervisord -n&
echo "Supervisor Started..."

exec /usr/local/bin/docker-php-entrypoint "$@"