#!/bin/bash

# Build the background tasks worker.
#
# This should be identical to build-web.sh minus building the front-end assets,
# as those are not currently needed for the background tasks worker.

# Dump optimized Composer autoloader using APCu for production.
#
# @see https://getcomposer.org/doc/articles/autoloader-optimization.md
composer dump-autoload --optimize --apcu

# Secure the file system by removing write permissions.
#
# @see https://stackoverflow.com/questions/6659689/referring-to-a-file-relative-to-executing-script#6659698
source "${BASH_SOURCE%/*}/secure-filesystem.sh"
