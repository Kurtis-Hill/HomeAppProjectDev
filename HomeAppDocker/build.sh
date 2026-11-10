#!/bin/sh
# Builds the HomeApp images. Bases are always built first (see docker-bake.hcl).
#
#   ./build.sh dev                  Build dev images (homeapp-*:dev) for docker-compose.yml
#   ./build.sh prod                 Build prod images for amd64 + arm64 (checks they build; nothing is saved)
#   ./build.sh prod --push          Build prod images for amd64 + arm64 and push them to the registry
#   ./build.sh prod --load          Build prod images for this machine only and load them into Docker (for testing)
#
# Options for prod:
#   --tag <tag>                     Tag to push as well as "latest" (e.g. 1.4.0 or a git commit)
#
# Settings can also be passed as environment variables, e.g.
#   REGISTRY=ghcr.io/someone WEBPACK_PUBLIC_PATH=/HomeApp/build ./build.sh prod --push

set -e

cd "$(dirname "$0")"

MODE="$1"
shift || true

PUSH=false
LOAD=false
while [ $# -gt 0 ]; do
  case "$1" in
    --push) PUSH=true ;;
    --load) LOAD=true ;;
    --tag) export TAG="$2"; shift ;;
    *) echo "Unknown option: $1"; exit 1 ;;
  esac
  shift
done

# GitHub token for Composer, passed to the build as a secret (never stored in an image)
SECRET_ARGS=""
if [ -f webservers/auth/auth.json ]; then
  SECRET_ARGS="--set php-app.secrets=id=composer_auth,src=webservers/auth/auth.json"
fi

case "$MODE" in
  dev)
    docker buildx bake -f docker-bake.hcl --builder default dev
    echo "Dev images built. Start them with: docker compose up -d"
    ;;

  prod)
    if [ "$PUSH" = true ] && [ "$LOAD" = true ]; then
      echo "Use either --push or --load, not both."
      exit 1
    fi

    if [ "$LOAD" = true ]; then
      # One platform only, so the images can be loaded into the local Docker
      PLATFORM="linux/$(docker version -f '{{.Server.Arch}}')"
      # shellcheck disable=SC2086
      docker buildx bake -f docker-bake.hcl --builder default prod $SECRET_ARGS \
        --set "*.platform=$PLATFORM" \
        --set "apache-prod.output=type=docker" \
        --set "php-cli-prod.output=type=docker" \
        --set "mariadb-prod.output=type=docker" \
        --set "redis-prod.output=type=docker" \
        --set "rabbitmq-prod.output=type=docker"
      echo "Prod images for $PLATFORM loaded into Docker."
      exit 0
    fi

    # Multi-platform builds need a docker-container builder (with QEMU for ARM)
    if ! docker buildx inspect homeapp-builder > /dev/null 2>&1; then
      echo "Creating buildx builder 'homeapp-builder'..."
      docker buildx create --name homeapp-builder --driver docker-container > /dev/null
    fi

    if [ "$PUSH" = true ]; then
      # shellcheck disable=SC2086
      docker buildx bake -f docker-bake.hcl --builder homeapp-builder prod $SECRET_ARGS --push
      echo "Prod images pushed."
    else
      # shellcheck disable=SC2086
      docker buildx bake -f docker-bake.hcl --builder homeapp-builder prod $SECRET_ARGS
      echo "Prod images built (not pushed). Add --push to push them."
    fi
    ;;

  *)
    sed -n '2,15p' "$0"
    exit 1
    ;;
esac
