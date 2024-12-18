#!/bin/sh

if [ "${APP_ENV}" = 'prod' ]; then
  echo "production container build"
  echo "installing composer packages..."
  git clean -f
  git pull origin master
  composer install --no-dev --optimize-autoloader --prefer-dist --no-interaction
  echo "Executing database migrations production..."
  bin/console doctrine:migrations:migrate -n
  echo "...Migrations complete"
fi

if [ "${APP_ENV}" = 'dev' ]; then
	echo "dev container build"
	echo "installing composer packages..."
  composer install --prefer-dist --no-interaction
	echo "Executing database migrations for test environment..."
	bin/console d:m:m --no-interaction --env=test
	echo "...Test migrations complete"

	echo "Executing database migrations for local environment..."
	bin/console doctrine:migrations:migrate -n
	echo "...Local migrations complete"

	##@TODO: not working
	echo "Querying test database"
	if [ php bin/console dbal:run-sql "select firstName from user" --env=test | grep 'array(1)']; then
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
	bin/console app:elastic-create-out-of-bounds-indices
	bin/console app:elastic-create-log-index
	echo "...Elastic indicie creation"
fi

# if [ ! -f "/var/www/html/config/jwt/private.pem" ]; then
#   echo "Generating JWT keys..."
php bin/console lexik:jwt:generate-keypair --overwrite
# fi

echo "Starting supervisor..."
supervisord -n -c /etc/supervisor/conf.d/update-current-reading.conf
echo "Supervisor Started..."

echo "Starting symfony transport..."
bin/console messenger:consume scheduler_default -vv
echo "transport started..."

exec "$@"
