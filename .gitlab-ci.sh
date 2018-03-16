#!/bin/bash

function generate_app_key {
    php -r "echo md5(uniqid()).\"\n\";"
}

set -xe

rm -f composer.lock

composer config -g repo.packagist composer https://packagist.phpcomposer.com
composer install

# Copy over testing configuration.
cp .env.testing .env

# Generate an application key
APP_KEY=$(generate_app_key)
sed -i -e s/APP_KEY=.*$/APP_KEY=${APP_KEY}/g .env

# Run database migrations.
php artisan test:import
