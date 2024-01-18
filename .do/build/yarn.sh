#!/bin/bash

# Because it's not yet possible to customize the order that autodetected
# buildpacks run their builds on DigitalOcean App Platform, and because the PHP
# buildpack always runs after the Node.js buildpack, we have to add these
# workspace items after the fact because they're only present after Composer has
# pulled them in and would otherwise stop the build with an error when Yarn
# would try to find them.
yarn add \
  "drupal-ambientimpact-core@workspace:^2" \
  "drupal-ambientimpact-icon@workspace:^1" \
  "drupal-ambientimpact-media@workspace:^4" \
  "drupal-ambientimpact-ux@workspace:^1" \
  "drupal-omnipedia-block@workspace:^6" \
  "drupal-omnipedia-changes@workspace:^7" \
  "drupal-omnipedia-content@workspace:^6" \
  "drupal-omnipedia-media@workspace:^6" \
  "drupal-omnipedia-site-theme@workspace:^6"

yarn build:deploy
