#!/bin/bash

# Create .htaccess in various directories to prevent PHP execution.
.do/build/write-htaccess.sh

# Run the Heroku PHP buildpack PHP-FPM with our custom config appended.
#
# @see https://devcenter.heroku.com/articles/custom-php-settings
#   Heroku PHP buildpack documentation for customizing configuration.
#
# @see https://github.com/heroku/heroku-buildpack-php/blob/main/bin/heroku-php-apache2
#   Commandline usage detailed in the print_help() function, including how to
#   pass this to the run command.
heroku-php-apache2 -C .apache/httpd.inc.conf -F .php/php-fpm.inc.conf web
