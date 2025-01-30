#!/bin/bash

# Enable maintenance mode.
drush maint:set 1
echo "=> Maintenance mode has been turned on"

# Run any necessary database updates.
drush -y updb

# Import any configuration changes. This should be run after database updates.
drush -y config:import

# Delete old tables from the Commerce days.
#
# @see https://github.com/neurocracy/omnipedia/issues/52
drush php:script .do/delete-old-tables.php
