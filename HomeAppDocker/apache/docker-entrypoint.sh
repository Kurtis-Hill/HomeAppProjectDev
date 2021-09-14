#!/bin/bash
set -e

sed -E '/xdebug\.remote_host=.+/d' /usr/local/etc/php/conf.d/xdebug.ini > /usr/local/etc/php/conf.d/xdebug.ini.tmp && mv /usr/local/etc/php/conf.d/xdebug.ini.tmp /usr/local/etc/php/conf.d/xdebug.ini
# echo "xdebug.remote_host=$WSLIP" >> /usr/local/etc/php/conf.d/xdebug.ini

# first arg is `-f` or `--some-option`
if [ "${1#-}" != "$1" ]; then
	set -- apache2-foreground "$@"
fi

echo "Installing composer packages..."

#php -d memory_limit=-1 `which composer` install --prefer-dist --no-interaction

echo "...Composer packages installed"

echo "Querying test database"



## not working as intended needs fixing ##
# if ! php bin/console dbal:run-sql "select * from user limit 1" --env=test > /dev/null -gt 1; then
#     echo "No test database found loading fixtures"
#     php bin/console doctrine:fixtures:load --no-interaction --env=test
#     echo "...Fixtures loaded"
# else
#     echo "Test database found"
# fi

# if [ ! -f /etc/logs/server-errors.log ]; then
#     touch /etc/logs/server-errors.log
# fi

# if [ ! -f /etc/logs/user-input-error.log ]; then
#     touch /etc/logs/server-errors.log
# fi

exec /usr/local/bin/docker-php-entrypoint "$@"

## not working as intended needs fixing ##
# if [ ! php bin/console dbal:run-sql "select * from user limit 1" --env=test ]; then
#     $1;
# else
#     echo "Test database found"
# fi
