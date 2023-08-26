#!/bin/bash

# Delete the Hux discovery cache entry in case of hook changes.
#
# @see https://www.drupal.org/project/hux/issues/3302312#comment-15202523
drush php:eval "\Drupal::service('cache.bootstrap')->delete('hux.discovery');"

# Run any necessary database updates.
drush -y updb
