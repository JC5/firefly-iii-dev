#!/bin/bash

set -e -o pipefail

cd /usr/src/dev-tools/

node --version

if [[ "output" == "$2" ]]; then

  php ./cli.php $1 > $FIREFLY_III_ROOT/output.txt
  echo "result=none" >> $GITHUB_OUTPUT
else
  php ./cli.php $1
  echo "result=none" >> $GITHUB_OUTPUT
fi
