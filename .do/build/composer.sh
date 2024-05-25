#!/bin/bash

# Dump optimized Composer autoloader using APCu for production.
#
# @see https://getcomposer.org/doc/articles/autoloader-optimization.md
composer dump-autoload --optimize --apcu
