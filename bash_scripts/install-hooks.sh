#!/bin/bash

GIT_DIR=$(git rev-parse --git-dir)

echo "🔧 Installing hooks...";
ln -s ../../bash_scripts/pre-commit.sh $GIT_DIR/hooks/pre-commit
echo "✅ Done!";
