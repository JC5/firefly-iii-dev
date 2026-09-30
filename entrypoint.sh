#!/bin/bash

set -e -o pipefail

cd /usr/src/dev-tools

rm -rf vendor
rm -rf composer.lock
rm -rf .env

curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.40.8/install.sh | bash

export NVM_DIR="/root/.nvm"
[ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh"  # This loads nvm
[ -s "$NVM_DIR/bash_completion" ] && \. "$NVM_DIR/bash_completion"  # This loads nvm bash_completion


npm install

php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php > /dev/null
php -r "unlink('composer-setup.php');"

mv composer.phar /usr/local/bin/composer

composer install -q

if [[ "output" == "$2" ]]; then
  php cli.php $1 > $FIREFLY_III_ROOT/output.txt
  echo "result=none" >> $GITHUB_OUTPUT
else
  php cli.php $1
  echo "result=none" >> $GITHUB_OUTPUT
fi
