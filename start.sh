#!/bin/bash
set -e

echo "==> [start.sh] 1. Creating storage and font directories..."
mkdir -p "storage/app/private/docs"
mkdir -p "resources/fonts"

echo "==> [start.sh] 2. Fixing permissions for www-data..."
chown -R www-data:www-data public storage bootstrap/cache resources/fonts
chmod -R ug+rwX public storage bootstrap/cache resources/fonts

echo "==> [start.sh] 3. Running composer install..."
composer install --quiet --no-progress --no-interaction --optimize-autoloader

echo "==> [start.sh] 4. Running Laravel setup commands..."
if [ -z "$APP_KEY" ]; then
  su -s /bin/sh www-data -c "php artisan key:generate --force || true"
fi
su -s /bin/sh www-data -c "php artisan storage:link --force || true"
su -s /bin/sh www-data -c "php artisan optimize:clear || true"

# Only generate passport keys if they don't already exist (avoids 20-30s startup delay)
if [ ! -f "storage/oauth-private.key" ]; then
  echo "==> Generating Passport OAuth keys..."
  su -s /bin/sh www-data -c "php artisan passport:keys --force || true"
fi

echo "==> [start.sh] 5. Running database migrations..."
if [ "$APP_ENV" = "production" ]; then
  su -s /bin/sh www-data -c "php artisan migrate --force || true"
else
  su -s /bin/sh www-data -c "php artisan migrate || true"
fi

echo "==> [start.sh] 6. Starting Apache web server..."
exec apache2-foreground
