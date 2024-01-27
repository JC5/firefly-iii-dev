#!/bin/sh

cd /usr/src/dev-tools

rm -rf vendor
rm -rf composer.lock
rm -rf .env

php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('sha384', 'composer-setup.php') === 'e21205b207c3ff031906575712edab6f13eb0b361f2085f1f1237b7126d785e826a450292b6cfd1d64d92e6563bbde02') {  } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
php composer-setup.php > /dev/null
php -r "unlink('composer-setup.php');"

mv composer.phar /usr/local/bin/composer

composer install -q

php cli.php $1
