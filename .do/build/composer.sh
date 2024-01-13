#!/bin/bash

# We need to remove this since it's not used in production but it is needed for
# running PHPUnit functional tests in CI. Trying to conditionally remove it via
# 'composer config --unset extra.drupal-scaffold.file-mapping."[web-root]/.htaccess"'
# or multiple such variations with various characters in the last key escaped
# seem to have no effect, despite the fact that fetching the config by leaving
# out '--unset' works correctly.
#
# @todo Figure out why running the above command doesn't remove the key, add it
#   back to the scaffold, and remove this command.
rm /workspace/web/.ht.router.php

# Dump optimized Composer autoloader using APCu for production.
#
# @see https://getcomposer.org/doc/articles/autoloader-optimization.md
composer dump-autoload --optimize --apcu
