#!/bin/bash
set -e
if [ "${1#-}" != "$1" ]; then
        set -- node "$@"
fi

git config --global url."https://".insteadOf git://

## If having trouble building for production drop the yarn.lock file and remove node_modules
if [ ${APP_ENV} = 'prod' ]; then
   echo "production environment installing..."
   yarn install --production --frozen-lockfile --check-files
   echo "setting network timeout for slower devices..."
   yarn config set network-timeout 600000 -g
#   yarn add --dev @symfony/webpack-encore
   yarn add @symfony/webpack-encore
   echo "building assets..."
   yarn build
   echo "...finished building assets"
   echo "yarn finished opertations"
fi

if [ ${APP_ENV} = 'dev' ]; then
   echo "development environment setting up webpack dev server..."
   yarn install --dev --check-files
   yarn watch
#    hot reload working but getting mixed content block https needs to be set for webpack
#    yarn encore dev-server --hot --host=apache --port ${HTTPS_APP_PORT}
#    yarn encore dev-server --hot  --disable-host-check --port ${HTTPS_APP_PORT}
   echo "... encore dev server began"
fi

exec "$@"
