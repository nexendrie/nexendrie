#!/bin/bash
cp app/config/local.sample.neon app/config/local.neon

mkdir app/temp
mkdir app/temp/cache
mkdir app/temp/sessions
mkdir app/log

curl -sS https://getcomposer.org/installer | php
mv composer.phar composer
chmod +x composer
./composer install -a --ignore-platform-reqs

exit 0
