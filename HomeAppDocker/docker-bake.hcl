# Builds every HomeApp image. Use ./build.sh rather than calling this directly.
#
# Each service has Dockerfile.base / Dockerfile.dev / Dockerfile.prod. The .dev and .prod files
# start "FROM homeapp-<service>-base"; the `contexts` entries below make that name point at the
# base target, so bake always builds the base first and nothing has to be pushed or tagged in between.
#
#   docker buildx bake dev            -> local dev images (homeapp-*:dev), loaded into Docker
#   docker buildx bake prod --push    -> multi-arch prod images pushed to ${REGISTRY}

variable "REGISTRY" {
  default = "ghcr.io/kurtis-hill"
}

variable "TAG" {
  default = "latest"
}

# CPU types the prod images are built for (the server is ARM; amd64 lets them run on a normal PC too)
variable "PLATFORMS" {
  default = "linux/amd64,linux/arm64"
}

# Where the browser loads the built assets from. Prod sits behind the nginx proxy, which serves
# them at /HomeApp/build/. Use WEBPACK_PUBLIC_PATH=/build for a prod build accessed directly.
variable "WEBPACK_PUBLIC_PATH" {
  default = "/HomeApp/build"
}

function "prod_tags" {
  params = [name]
  result = TAG == "latest" ? ["${REGISTRY}/${name}:latest"] : ["${REGISTRY}/${name}:${TAG}", "${REGISTRY}/${name}:latest"]
}

group "dev" {
  targets = ["apache-dev", "php-cli-dev", "yarn-dev", "mariadb-dev", "redis-dev", "rabbitmq-dev"]
}

group "prod" {
  targets = ["apache-prod", "php-cli-prod", "mariadb-prod", "redis-prod", "rabbitmq-prod"]
}

# ---------- Base images (shared by dev and prod; never pushed) ----------

target "php-base" {
  context    = "."
  dockerfile = "webservers/php-base/Dockerfile.base"
}

target "node-base" {
  context    = "."
  dockerfile = "yarn/Dockerfile.base"
}

target "mariadb-base" {
  context    = "."
  dockerfile = "mariadb/Dockerfile.base"
}

target "redis-base" {
  context    = "."
  dockerfile = "redis/Dockerfile.base"
}

target "rabbitmq-base" {
  context    = "."
  dockerfile = "rabbitmq/Dockerfile.base"
}

# Prod builds need the bases for every prod CPU type
target "_prod" {
  platforms = split(",", PLATFORMS)
}

target "php-base-prod" {
  inherits = ["php-base", "_prod"]
}

target "node-base-prod" {
  inherits = ["node-base", "_prod"]
}

target "mariadb-base-prod" {
  inherits = ["mariadb-base", "_prod"]
}

target "redis-base-prod" {
  inherits = ["redis-base", "_prod"]
}

target "rabbitmq-base-prod" {
  inherits = ["rabbitmq-base", "_prod"]
}

# ---------- Dev images (app code is mounted, so nothing of the app is baked in) ----------

target "_dev" {
  output = ["type=docker"]
}

target "apache-dev" {
  inherits   = ["_dev"]
  context    = "."
  dockerfile = "webservers/apache/Dockerfile.dev"
  contexts   = { homeapp-php-base = "target:php-base" }
  tags       = ["homeapp-apache:dev"]
}

target "php-cli-dev" {
  inherits   = ["_dev"]
  context    = "."
  dockerfile = "webservers/php-cli/Dockerfile.dev"
  contexts   = { homeapp-php-base = "target:php-base" }
  tags       = ["homeapp-php-cli:dev"]
}

target "yarn-dev" {
  inherits   = ["_dev"]
  context    = "."
  dockerfile = "yarn/Dockerfile.dev"
  contexts   = { homeapp-node-base = "target:node-base" }
  tags       = ["homeapp-yarn:dev"]
}

target "mariadb-dev" {
  inherits   = ["_dev"]
  context    = "."
  dockerfile = "mariadb/Dockerfile.dev"
  contexts   = { homeapp-mariadb-base = "target:mariadb-base" }
  tags       = ["homeapp-mariadb:dev"]
}

target "redis-dev" {
  inherits   = ["_dev"]
  context    = "."
  dockerfile = "redis/Dockerfile.dev"
  contexts   = { homeapp-redis-base = "target:redis-base" }
  tags       = ["homeapp-redis:dev"]
}

target "rabbitmq-dev" {
  inherits   = ["_dev"]
  context    = "."
  dockerfile = "rabbitmq/Dockerfile.dev"
  contexts   = { homeapp-rabbitmq-base = "target:rabbitmq-base" }
  tags       = ["homeapp-rabbitmq:dev"]
}

# ---------- Prod build steps (not pushed) ----------

# PHP base + app code, vendor/ and warmed cache. Shared by apache-prod and php-cli-prod.
target "php-app" {
  inherits   = ["_prod"]
  context    = ".."
  dockerfile = "HomeAppDocker/webservers/php-base/Dockerfile.app"
  contexts   = { homeapp-php-base = "target:php-base-prod" }
}

# Built front-end assets (public/build), copied into apache-prod
target "assets" {
  inherits   = ["_prod"]
  context    = ".."
  dockerfile = "HomeAppDocker/yarn/Dockerfile.prod"
  contexts   = { homeapp-node-base = "target:node-base-prod" }
  args       = { WEBPACK_PUBLIC_PATH = WEBPACK_PUBLIC_PATH }
}

# ---------- Prod images (pushed to the registry) ----------

target "apache-prod" {
  inherits   = ["_prod"]
  context    = "."
  dockerfile = "webservers/apache/Dockerfile.prod"
  contexts   = {
    homeapp-php-app = "target:php-app"
    homeapp-assets  = "target:assets"
  }
  tags = prod_tags("homeapp-apache")
}

target "php-cli-prod" {
  inherits   = ["_prod"]
  context    = "."
  dockerfile = "webservers/php-cli/Dockerfile.prod"
  contexts   = { homeapp-php-app = "target:php-app" }
  tags       = prod_tags("homeapp-php-cli")
}

target "mariadb-prod" {
  inherits   = ["_prod"]
  context    = "."
  dockerfile = "mariadb/Dockerfile.prod"
  contexts   = { homeapp-mariadb-base = "target:mariadb-base-prod" }
  tags       = prod_tags("homeapp-mariadb")
}

target "redis-prod" {
  inherits   = ["_prod"]
  context    = "."
  dockerfile = "redis/Dockerfile.prod"
  contexts   = { homeapp-redis-base = "target:redis-base-prod" }
  tags       = prod_tags("homeapp-redis")
}

target "rabbitmq-prod" {
  inherits   = ["_prod"]
  context    = "."
  dockerfile = "rabbitmq/Dockerfile.prod"
  contexts   = { homeapp-rabbitmq-base = "target:rabbitmq-base-prod" }
  tags       = prod_tags("homeapp-rabbitmq")
}
