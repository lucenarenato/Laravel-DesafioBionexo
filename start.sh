#!/bin/bash

chmod -R 777 storage/
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate --seed
./vendor/bin/sail artisan cache:clear
./vendor/bin/sail artisan config:cache
./vendor/bin/sail composer dump-autoload -o
./vendor/bin/sail artisan l5-swagger:generate
