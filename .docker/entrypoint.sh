#!/bin/bash

#On error no such file entrypoint.sh, execute in terminal - dos2unix .docker\entrypoint.sh
cp .env.example .env
cp .env.testing.example .env.testing
composer install
php artisan key:generate
php artisan migrate
php artisan ide-helper:generate
php artisan ide-helper:model > /dev/null << EOF
<no>
EOF

php artisan storage:link

chmod -R 777 ./

php-fpm
