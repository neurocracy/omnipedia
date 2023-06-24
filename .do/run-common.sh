#!/bin/bash

# Create .htaccess in files directories to prevent PHP execution.
#
# Creates the necessary .htaccess files to public, private, and asset
# directories to prevent execution of PHP. While probably not necessary for the
# public and private files because the S3 File System module is configured to
# take over the public:// and private:// stream wrappers, and executing PHP is
# not possible from the DigitalOcean Spaces buckets, this ensures the local
# assets directory does contain this file after a deploy.
#
# Note that this must be in the run phase because the database is not available
# during the build phase and this would have no effect.
#
# @see \Drupal\Core\File\HtaccessWriter::ensure()
drush php:eval "\Drupal::service('file.htaccess_writer')->ensure();"

# Create .htaccess in (unused) local public files for Security Review module.
#
# The Security Review module will incorrectly flag this as a failure for the
# executable_php check even though the S3 File System module is configured to
# take over the public:// and private:// stream wrappers.
#
# Note that this must be in the run phase because the database is not available
# during the build phase and this would have no effect.
#
# @see \Drupal\Core\File\HtaccessWriter::write()
drush php:eval "\Drupal::service('file.htaccess_writer')->write('sites/default/files', false);"
