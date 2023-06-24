#!/bin/bash

# This secures the filesystem by removing write permissions.
#
# @see https://www.drupal.org/docs/security-in-drupal/securing-file-permissions-and-ownership
#   Loosely based on this, adapted for DigitalOcean's App Platform.
#
# @see https://www.gnu.org/software/findutils/manual/html_mono/find.html
#   GNU find documentation.

echo "=> Removing write permissions:";
for d in /workspace/drupal/*
do
  find $d -execdir chmod u-w,g-w,o= '{}' \; -print
done

# Restore write permissions to the default public files directory so that
# /admin/reports/status doesn't complain, even though the S3 File System module
# is configured to take over the public:// stream wrapper.
chmod u+w /workspace/drupal/sites/default/files
