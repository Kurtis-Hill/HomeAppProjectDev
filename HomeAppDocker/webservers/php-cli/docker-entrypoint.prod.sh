#!/bin/sh
# Prod entrypoint: code and vendor/ are already in the image, so no composer install here.

set -e

echo "Executing database migrations..."
php bin/console doctrine:migrations:migrate -n --allow-no-migration
echo "...Migrations complete"

if [ "${ELASTIC_ENABLED}" = 'true' ]; then
  echo "Waiting for Elasticsearch to be ready..."
  ELASTIC_HOST="${ELASTIC_HOST:-es01}"
  ELASTIC_PORT="${ELASTIC_PORT:-9200}"
  retries=30
  until curl -sf "http://${ELASTIC_HOST}:${ELASTIC_PORT}/_cluster/health?wait_for_status=yellow&timeout=5s" > /dev/null 2>&1; do
    retries=$((retries - 1))
    if [ "$retries" -le 0 ]; then
      echo "ERROR: Elasticsearch not reachable after waiting. Skipping index creation."
      break
    fi
    echo "  ...Elasticsearch not ready yet, retrying in 5s (${retries} attempts left)"
    sleep 5
  done

  if curl -sf "http://${ELASTIC_HOST}:${ELASTIC_PORT}/_cluster/health" > /dev/null 2>&1; then
    echo "Elastic index creation"
    php bin/console app:elastic-create-const-record-indices
    php bin/console app:elastic-create-out-of-bounds-indices
    php bin/console app:elastic-create-log-index
    echo "...Elastic index creation complete"
  fi
fi

# Keys live on a volume shared with the apache container. Only create them the first time,
# so existing logins keep working across restarts.
echo "Checking JWT keys..."
php bin/console lexik:jwt:generate-keypair --skip-if-exists
chown www-data:www-data config/jwt/*.pem
chmod 600 config/jwt/private.pem

exec "$@"
