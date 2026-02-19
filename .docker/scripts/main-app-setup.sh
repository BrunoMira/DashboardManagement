#!/bin/sh

rm -rf public/storage
ln -s ../storage/app/public public/storage

composer install --no-ansi --no-interaction --no-plugins --no-progress --no-scripts --optimize-autoloader

cp ./.docker/root.htaccess ./.htaccess

cp ./.docker/public.htaccess ./public/.htaccess

if [ ! -f .env ]; then
    cp .env.example .env
fi

php .docker/scripts/test-mysql.php

php artisan migrate --force

php artisan db:seed

php artisan optimize:clear

exec php-fpm
