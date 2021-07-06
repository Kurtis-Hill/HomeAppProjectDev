#!/bin/bash
set -e

echo "Installing composer packages..."

# TODO remember to un comment

php -d memory_limit=-1 `which composer` install --prefer-dist --no-interaction

echo "...Composer packages installed"

echo "Querying test database"

if ! php bin/console dbal:run-sql "select * from user limit 1"> /dev/null --env=test; then
    echo "No test database found loading fixtures"
    php bin/console doctrine:fixtures:load --no-interaction
    echo "...Fixtures loaded"
else 
    echo "Test database found"
fi    

exec /usr/local/bin/docker-php-entrypoint "$@"
