#!/bin/bash

# Hardens packages by deleting test files and .git directories so that those
# are not part of the deployment.
#
# @see https://packagist.org/packages/drupal/core-vendor-hardening
#   Similar to this Drupal core package but since that package doesn't seem to
#   support patterns, adding every package would be very tedious.

# Required to use '**'
shopt -s globstar

delete_test_directories() {

  for directory in "$1"/**/tests; do
    if [ -d "$directory" ]; then
      echo $directory
      rm -rf $directory
    fi
  done

  for directory in "$1"/**/src/Tests; do
    if [ -d "$directory" ]; then
      echo $directory
      rm -rf $directory
    fi
  done

}

delete_git_directories() {

  for directory in "$1"/**/.git; do
    if [ -d "$directory" ]; then
      echo $directory
      rm -rf $directory
    fi
  done

}

echo '=> Deleting test directories:';

delete_test_directories '/workspace/web/core'
delete_test_directories '/workspace/web/modules'
delete_test_directories '/workspace/web/profiles'
delete_test_directories '/workspace/web/themes'

echo '=> Finished deleting test directories.';

echo '=> Deleting .git directories:';

delete_git_directories '/workspace/web/core'
delete_git_directories '/workspace/web/modules'
delete_git_directories '/workspace/web/profiles'
delete_git_directories '/workspace/web/themes'

echo '=> Finished deleting .git directories.';
