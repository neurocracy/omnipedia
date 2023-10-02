#!/bin/bash

# @see https://stackoverflow.com/questions/6659689/referring-to-a-file-relative-to-executing-script#6659698
source "${BASH_SOURCE%/*}/run-common.sh"

# Run the Heroku PHP buildpack PHP-FPM with our custom config appended.
#
# @see https://devcenter.heroku.com/articles/custom-php-settings
#   Heroku PHP buildpack documentation for customizing configuration.
#
# @see https://github.com/heroku/heroku-buildpack-php/blob/main/bin/heroku-php-apache2
#   Commandline usage detailed in the print_help() function, including how to
#   pass this to the run command.
heroku-php-apache2 -C .apache/httpd.inc.conf -F .php/php-fpm.inc.conf \
  # This automagically gets the web root from Drush so we don't have to hard
  # code it.
  $(drush drupal:directory root)
