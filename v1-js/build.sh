#!/bin/bash
echo "Build old JS from $FIREFLY_III_ROOT"


SCRIPT_DIR="$( cd -- "$( dirname -- "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )"

echo "Script dir is $SCRIPT_DIR"


rm -rf $SCRIPT_DIR/public
rm -rf $SCRIPT_DIR/resources

cp -r $FIREFLY_III_ROOT/resources $SCRIPT_DIR

cd $SCRIPT_DIR

yarn install
yarn upgrade
yarn prod

cp $SCRIPT_DIR/public/v1/js/*.js $FIREFLY_III_ROOT/public/v1/js/
cp $SCRIPT_DIR/public/v1/js/*.LICENSE.txt $FIREFLY_III_ROOT/public/v1/js/
cp $SCRIPT_DIR/public/v1/js/webhooks/*.js $FIREFLY_III_ROOT/public/v1/js/webhooks/
cp $SCRIPT_DIR/public/v1/js/webhooks/*.LICENSE.txt $FIREFLY_III_ROOT/public/v1/js/webhooks/
