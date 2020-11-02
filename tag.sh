#!/bin/bash

# Prompt the version
read -p "Version: " version

# Check if the version already exists
if [ $(git tag -l "$version") ]; then
    echo "Version already exists!"
    exit
fi

# Create and push the tag
git tag $version
git push --tags

echo "Done."
