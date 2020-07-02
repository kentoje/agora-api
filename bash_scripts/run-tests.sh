#!/bin/bash

set -e

cd "${0%/*}/.."

echo "Running tests"
echo "............................"

if php bin/phpunit ; then
    echo "✅ Command succeeded"
else
    echo "❌ Failed!" && exit 1
fi

