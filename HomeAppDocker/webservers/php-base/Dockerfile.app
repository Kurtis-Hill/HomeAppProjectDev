# Prod only: the base image plus the application code, vendor/ and a warmed cache.
# Built as homeapp-php-app and used as the starting point for apache and php-cli prod images.
# Build context is the repository root (see docker-bake.hcl).
FROM homeapp-php-base

ENV APP_ENV=prod \
    APP_DEBUG=0

# Install dependencies first so this layer is cached until composer.lock changes.
# auth.json (GitHub token for Composer rate limits) is passed as a build secret, so it never ends up in the image.
COPY app/composer.json app/composer.lock app/symfony.lock ./
RUN --mount=type=secret,id=composer_auth,target=/root/.composer/auth.json,required=false \
    composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction --no-progress

COPY app/ ./

# The image gets the committed .env.example as its .env. It only holds defaults and
# ${VAR} references; real values (passwords etc.) come from environment variables at runtime.
RUN cp .env.example .env \
    && composer dump-autoload --no-dev --optimize \
    && php bin/console cache:warmup --env=prod \
    && php bin/console assets:install public --env=prod \
    && chown -R www-data:www-data var assets/images/profile-pics
