#!/bin/bash
set -e

# yarn install
if [ "${1#-}" != "$1" ]; then
        set -- node "$@"
fi

git config --global url."https://".insteadOf git://

yarn install

yarn encore dev --watch

exec "$@"
