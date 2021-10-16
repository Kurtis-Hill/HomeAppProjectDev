#!/bin/bash
set -e

if [ "${1#-}" != "$1" ]; then
        set -- node "$@"
fi

git config --global url."https://".insteadOf git://

yarn install

if [ ${APP_ENV} == 'prod' ]; then
   echo "production enviroment installing yarn assets..."
   yarn install
   echo "...finished installing assets"
fi

if [ ${APP_ENV} == 'dev' ]; then
   echo "development enviroment setting up webpack dev server..."
   yarn install --check-files      
   yarn encore dev --watch
   yarn encore dev-server --hot --host=apache --port 8080
   echo "... encore dev server began"
fi
        


exec "$@"
