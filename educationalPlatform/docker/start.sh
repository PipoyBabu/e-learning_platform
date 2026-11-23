#!/usr/bin/env bash
set -e

mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs
chown -R www-data:www-data storage bootstrap/cache

php artisan migrate --force

if [ "${RUN_SEED:-false}" = "true" ]; then
    php artisan db:seed --force
fi

php artisan config:cache
php artisan route:cache
php artisan view:cache

exec /usr/bin/supervisord -n -c /etc/supervisord.conf
