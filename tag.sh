RED='\033[0;31m'
GREEN='\033[0;32m'

# Get the version
NEW_VERSION=$(cat .version)

# Check if the version already exists
if [ $(git tag -l "$NEW_VERSION") ]; then
    echo "${RED}Version already exists!"
    exit
fi

# Create and push the tag
git tag $NEW_VERSION
git push --tags

echo "${GREEN}Done."
