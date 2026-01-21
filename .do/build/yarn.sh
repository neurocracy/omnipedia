#!/bin/bash

# @see https://superuser.com/questions/270214/how-can-i-change-the-colors-of-my-xterm-using-ansi-escape-sequences
RED='\033[31m'
GREEN='\033[32m'
YELLOW='\033[33m'
BLUE='\033[34m'
MAGENTA='\033[35m'
CYAN='\033[36m'
RESET='\033[0m'

# Because it's not yet possible to customize the order that autodetected
# buildpacks run their builds on DigitalOcean App Platform, and because the PHP
# buildpack always runs after the Node.js buildpack, we have to add these
# workspace items after the fact because they're only present after Composer has
# pulled them in and would otherwise stop the build with an error when Yarn
# would try to find them. To accomplish this, they're instead listed in a
# "composerDependencies" key and not under "dependencies" or "devDependencies",
# and then during the build phase we parse that using jq, format it into the
# syntax for yarn add, and use command substitution to provide it all to Yarn.
#
# @see https://jqlang.github.io/jq/
#
# @see Aptfile
#   Tells App Platform to install jq for us.

# This is the key we're expecting to find dependencies that will be pulled in
# via Composer in.
COMPOSER_KEY='composerDependencies'

if [[ $(jq 'has($composerKey)' package.json \
  --arg composerKey $COMPOSER_KEY \
) == 'true' ]]; then

  KEY_TYPE="$(jq '.[$composerKey] | type' package.json \
    --arg composerKey $COMPOSER_KEY \
    --raw-output \
  )"

  if [[ $KEY_TYPE != "object" ]]; then

    printf "🛑 ${RED}The \"$COMPOSER_KEY\" in your package.json must be an object; found $KEY_TYPE instead.${RESET}\n" >&2

    exit 1

  fi

  DEPENDENCIES="$(jq \
    '.[$composerKey] | to_entries[] | [.key, .value] | [join("@")] | join(" ")' \
    --arg composerKey $COMPOSER_KEY \
    --raw-output \
    package.json \
  )"

  if [[ -n $DEPENDENCIES ]]; then

    yarn add $DEPENDENCIES

  fi

fi

yarn build:deploy
