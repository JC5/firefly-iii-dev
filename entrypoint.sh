#!/bin/bash

set -e -o pipefail

if [[ "output" == "$2" ]]; then
  php /usr/src/dev-tools/cli.php $1 > $FIREFLY_III_ROOT/output.txt
  echo "result=none" >> $GITHUB_OUTPUT
else
  php /usr/src/dev-tools/cli.php $1
  echo "result=none" >> $GITHUB_OUTPUT
fi
