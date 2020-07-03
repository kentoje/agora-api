#!/bin/bash

function changeAppEnv() {
    echo "✨ Changing APP_ENV to $1"
    echo "............................"
    sed -i ".env.local" '/APP_ENV/d' .env.local
    echo "APP_ENV=$1" >> .env.local
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
