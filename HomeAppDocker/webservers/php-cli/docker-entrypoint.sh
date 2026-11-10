#!/bin/sh

set -e

if [ "${APP_ENV}" = 'prod' ]; then
  echo "production container build"
  echo "installing composer packages..."
  #git clean -f
  #git pull origin master
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

  if [ "$(php bin/console dbal:run-sql 'SELECT COUNT(*) FROM users' --env=test | grep -o '[0-9]\+' | tail -1)" = "1" ]; then
    echo "Test database has one user, loading fixtures..."
    php bin/console doctrine:fixtures:load --no-interaction --env=test
    echo "...Fixtures loaded"
  fi

fi

if [ "${ELASTIC_ENABLED}" = 'true' ]; then
  echo "Waiting for Elasticsearch to be ready..."
  ELASTIC_HOST="${ELASTIC_HOST:-es01}"
  ELASTIC_PORT="${ELASTIC_PORT:-9200}"
  retries=30
  until curl -sf "http://${ELASTIC_HOST}:${ELASTIC_PORT}/_cluster/health?wait_for_status=yellow&timeout=5s" > /dev/null 2>&1; do
    retries=$((retries - 1))
    if [ "$retries" -le 0 ]; then
      echo "ERROR: Elasticsearch not reachable after waiting. Aborting index creation."
      break
    fi
    echo "  ...Elasticsearch not ready yet, retrying in 5s (${retries} attempts left)"
    sleep 5
  done

  if curl -sf "http://${ELASTIC_HOST}:${ELASTIC_PORT}/_cluster/health" > /dev/null 2>&1; then
    echo "Elastic indicie creation"
    bin/console app:elastic-create-const-record-indices
    bin/console app:elastic-create-out-of-bounds-indices
    bin/console app:elastic-create-log-index
    echo "...Elastic indicie creation"
  fi
fi

#if [ ! -f "/var/www/html/config/jwt/private.pem" ]; then
  echo "Generating JWT keys..."
  php bin/console lexik:jwt:generate-keypair --overwrite
#fi

echo "Starting symfony transport..."
bin/console messenger:consume scheduler_default -vv --failure-limit=3 &
echo "transport started..."

echo "Starting supervisor..."
supervisord -n -c /etc/supervisor/conf.d/update-current-reading.conf
echo "Supervisor Started..."

bin/console cache:clear

exec "$@"
