#!/bin/bash
set -e

if [ "${1#-}" != "$1" ]; then
        set -- node "$@"
fi

git config --global url."https://".insteadOf git://

yarn install

if [ ${APP_ENV} != 'prod' ]; then
   yarn encore dev --watch
   yarn encore dev-server --hot --host=apache --port 8080
fi

if [ ${APP_ENV} != 'dev' ]; then
   yarn install --check-files      
   yarn encore dev --watch
   yarn encore dev-server --hot --host=apache --port 8080
fi
        


exec "$@"
