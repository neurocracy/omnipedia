#!/bin/bash

# Dump optimized Composer autoloader using APCu for production.
#
# @see https://getcomposer.org/doc/articles/autoloader-optimization.md
composer dump-autoload --optimize --apcu

# Only scaffold our .htaccess prepend and append if running this script, i.e. on
# App Platform. We want to allow access to install.php when running via DDEV
# and only block that in production.
#
# @see https://gitlab.com/neurocracy/digitalocean/drupal-app-platform-template/-/issues/6
composer config --json --merge 'extra.drupal-scaffold.file-mapping' \
  '{"[web-root]/.htaccess": {"prepend": "drupal_scaffold/htaccess_prepend", "append": "drupal_scaffold/htaccess_append"}}' &&
composer drupal:scaffold
