#!/bin/bash

# Rebuild asset libraries as a temporary workaround for local aggregation.
#
# Since App Platform wipes the filesystem on every deploy, locally saved
# aggregated assets get wiped as well, and neither Drupal nor AdvAgg clue in
# that the files they'd previously aggregated no longer exist, which results in
# a 404. This is a temporary workaround to invalidate all the aggregated assets
# and cause them to be rebuilt so they exist in the new filesystem after a
# deploy.
#
# @todo Implement backing up aggregated assets to DigitalOcean Spaces when they
#   get created and attempt to pull them in after a deploy, only rebuilding
#   assets if any of them are not found.
drush rebuilder asset

# Run the Heroku PHP buildpack PHP-FPM with our custom config appended.
#
# @see https://devcenter.heroku.com/articles/custom-php-settings
#   Heroku PHP buildpack documentation for customizing configuration.
#
# @see https://github.com/heroku/heroku-buildpack-php/blob/main/bin/heroku-php-apache2
#   Commandline usage detailed in the print_help() function, including how to
#   pass this to the run command.
heroku-php-apache2 -F .php/php-fpm.inc.conf drupal
