#!/bin/sh

echo "Starting supervisor..."
supervisord -n&
echo "Supervisor Started..."

exec /usr/local/bin/docker-php-entrypoint "$@"