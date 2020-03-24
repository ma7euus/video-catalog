#!/bin/bash

#On error no such file entrypoint.sh, execute in terminal - dos2unix .docker\entrypoint.sh

## FRONT-END
npm config set cache /var/www/.npm-cache --global
cd /var/www/frontend && npm install && cd ..

## BACK-END
cd backend
if [ ! -f ".env" ]; then
  cp .env.example .env
fi
if [ ! -f ".env.testing" ]; then
  cp .env.testing.example .env.testing
fi

composer install
php artisan key:generate
php artisan migrate
php artisan ide-helper:generate
php artisan ide-helper:model > /dev/null << EOF
<no>
EOF

if [ ! -L "/var/www/backend/public/storage" ]; then
  php /var/www/backend/artisan storage:link
fi

chmod -R 777 ./

php-fpm
