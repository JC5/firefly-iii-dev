#!/bin/bash

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

if [[ "output" == "$2" ]]; then
  result=$(php cli.php $1)

  # echo $result

  #result="${result//'%'/'%25'}"
  #result="${result//$'\n'/'%0A'}"
  #result="${result//$'\r'/'%0D'}"
   {
          echo 'result<<EOF'
          php cli.php $1
          echo EOF
        } >> "$GITHUB_OUTPUT"

  # echo $result

  # echo "output=$result" >> $GITHUB_OUTPUT
  # echo "output=empty" >> $GITHUB_OUTPUT
  # echo "output=$result" >> $GITHUB_OUTPUT
  echo "result=$result" >> $GITHUB_OUTPUT
else
  php cli.php $1
  echo "result=none" >> $GITHUB_OUTPUT
fi
