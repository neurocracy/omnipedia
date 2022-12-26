#!/bin/bash

# Because it's not yet possible to customize the order that autodetected
# buildpacks run their builds on DigitalOcean App Platform, and because the PHP
# buildpack always runs after the Node.js buildpack, we have to add these
# workspace items after the fact because they're only present after Composer has
# pulled them in and would otherwise stop the build with an error when Yarn
# would try to find them.
yarn add \
  "drupal-omnipedia-block@workspace:^4" \
  "drupal-omnipedia-changes@workspace:^4" \
  "drupal-omnipedia-content@workspace:^4" \
  "drupal-omnipedia-media@workspace:^4" \
  "drupal-omnipedia-site-theme@workspace:^4"

yarn build:deploy

# @see https://stackoverflow.com/questions/6659689/referring-to-a-file-relative-to-executing-script#6659698
source "${BASH_SOURCE%/*}/secure-filesystem.sh"
