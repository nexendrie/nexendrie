#!/bin/bash
cp app/config/local.sample.neon app/config/local.neon

mkdir temp/cache
mkdir temp/sessions

curl -sS https://getcomposer.org/installer | php
mv composer.phar composer
chmod +x composer
./composer install -a --ignore-platform-reqs

exit 0
