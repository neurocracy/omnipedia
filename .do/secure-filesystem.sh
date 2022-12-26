#!/bin/bash

# This secures the filesystem by removing write permissions.
#
# @see https://www.drupal.org/docs/security-in-drupal/securing-file-permissions-and-ownership
#   Loosely based on this, adapted for DigitalOcean's App Platform.

# Set the version controlled default public files .htaccess to read-only so
# Security Review won't complain, even though that directory isn't used since we
# use the S3 File System module to take over the public:// stream wrapper.
chmod u-w /workspace/drupal/sites/default/files/.htaccess

echo "=> Removing write permissions:";
for d in /workspace/drupal/*
do
  find $d -exec echo '{}' \; -execdir chmod u-w,g-w,o= '{}' \;
done
