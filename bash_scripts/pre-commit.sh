#!/bin/bash

echo "✨ Running pre-commit hook"
./bash_scripts/run-tests.sh

if [ $? -ne 0 ]; then
 echo "❌ Tests must pass before commit!"
 exit 1
fi
