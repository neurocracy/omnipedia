#!/bin/bash

yarn add \
  "drupal-omnipedia-block@workspace:^4" \
  "drupal-omnipedia-changes@workspace:^4" \
  "drupal-omnipedia-content@workspace:^4" \
  "drupal-omnipedia-media@workspace:^4" \
  "drupal-omnipedia-site-theme@workspace:^4"

yarn build:deploy
