#!/bin/bash
set -e

if [ "${1#-}" != "$1" ]; then
        set -- node "$@"
fi

git config --global url."https://".insteadOf git://

# yarn install

if [ ${APP_ENV} = 'prod' ]; then
   echo "production environment installing yarn assets..."
   npm run build
   echo "...finished installing assets"
fi

if [ ${APP_ENV} = 'dev' ]; then
   echo "development environment setting up webpack dev server..."
   yarn install --check-files      
   yarn encore dev --watch
   # hot reload working but getting mixed content block
   # yarn encore dev-server --hot --host=apache --port ${HTTPS_APP_PORT}
   echo "... encore dev server began"
fi

exec "$@"
