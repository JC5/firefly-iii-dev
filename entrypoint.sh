#!/bin/bash

set -e -o pipefail

cd /usr/src/dev-tools/

export NVM_DIR="/root/.nvm"
[ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh"  # This loads nvm
[ -s "$NVM_DIR/bash_completion" ] && \. "$NVM_DIR/bash_completion"  # This loads nvm bash_completion

node --version

if [[ "output" == "$2" ]]; then

  php ./cli.php $1 > $FIREFLY_III_ROOT/output.txt
  echo "result=none" >> $GITHUB_OUTPUT
else
  php ./cli.php $1
  echo "result=none" >> $GITHUB_OUTPUT
fi
