#!/bin/bash
set -e

if [ "${1#-}" != "$1" ]; then
        set -- node "$@"
fi

git config --global url."https://".insteadOf git://

yarn install

yarn encore dev --watch

# yarn encore dev-server --hot --host=0.0.0.0 --port 8080

exec "$@"
