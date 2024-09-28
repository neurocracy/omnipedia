#!/bin/bash

# Enable maintenance mode.
drush maint:set 1
echo "=> Maintenance mode has been turned on"

# Rebuild plug-in definitions as these can occasionally cause errors if one or
# more has been removed.
drush rebuilder plugins

# Run any necessary database updates.
drush -y updb

# Import any configuration changes. This should be run after database updates.
drush -y config:import
