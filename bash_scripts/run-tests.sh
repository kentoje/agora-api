#!/bin/bash

FILE=.env.local

function changeAppEnv() {
    if test -f "$FILE"; then
        echo "✨ Changing APP_ENV to $1"
        echo "............................"
        sed -i ".env.local" '/APP_ENV/d' $FILE
        echo "APP_ENV=$1" >> $FILE
    else
        echo "😧 You have to create a .env.local file!" && exit 1
    fi
}

set -e

cd "${0%/*}/.."

changeAppEnv "test"

echo "Running tests"
echo "............................"

if php bin/phpunit ; then
    echo "✅ Command succeeded"
else
    changeAppEnv "dev"
    echo "❌ Failed!" && exit 1
fi

changeAppEnv "dev"
echo "🥰 Exiting"
