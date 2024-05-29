#!/bin/bash

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
yarn add $(jq --raw-output '.composerDependencies | to_entries[] | [.key, .value] | [join("@")] | join(" ")' package.json)

yarn build:deploy
