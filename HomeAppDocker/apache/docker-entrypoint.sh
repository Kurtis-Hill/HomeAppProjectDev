#!/bin/bash
set -e

echo "Installing composer packages..."

php -d memory_limit=-1 `which composer` install --prefer-dist --no-interaction

echo "...Composer packages installed"

echo "Querying test database"

## not working as intended needs fixing ##
if ! php bin/console dbal:run-sql "select * from user limit 1" --env=test > /dev/null -gt 1; then
    echo "No test database found loading fixtures"
    php bin/console doctrine:fixtures:load --no-interaction --env=test
    echo "...Fixtures loaded"
else 
    echo "Test database found"
fi    

exec /usr/local/bin/docker-php-entrypoint "$@"