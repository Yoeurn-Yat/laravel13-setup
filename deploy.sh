#!/bin/bash
set -e

# If running on host machine, automatically run inside Docker container
if [ ! -f /.dockerenv ]; then
  if [ -z "$(docker compose ps -q web 2>/dev/null)" ]; then
    echo "==> Docker containers are not running. Starting them first..."
    docker compose up -d
  fi
  echo "==> Delegating deployment to Docker container (web)..."
  docker compose exec web bash deploy.sh "$@"
  exit $?
fi

echo "==> 1. Preparing directories..."
mkdir -p storage/app/private/docs
mkdir -p resources/fonts

echo "==> 2. Setting permissions..."
chown -R www-data:www-data public storage bootstrap/cache resources/fonts
chmod -R ug+rwX public storage bootstrap/cache resources/fonts

echo "==> 3. Installing composer dependencies (scanning classmaps, ~30-45s)..."
composer install --no-interaction --optimize-autoloader

echo "==> 4. Running Laravel setup commands..."
if [ -z "$APP_KEY" ]; then
  su -s /bin/sh www-data -c "php artisan key:generate --force || true"
fi
su -s /bin/sh www-data -c "php artisan storage:link --force || true"
su -s /bin/sh www-data -c "php artisan optimize:clear || true"
su -s /bin/sh www-data -c "php artisan passport:keys --force || true"

echo "==> 5. Running database migrations..."
# su -s /bin/sh www-data -c "php artisan migrate --force || true"

su -s /bin/sh www-data -c "php artisan migrate || true"

echo "==> Deployment completed successfully!"
