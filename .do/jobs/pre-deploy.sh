#!/bin/bash

drush state:set digitalocean.deployment_active true

# Enable maintenance mode.
# drush maint:set 1
# echo "=> Maintenance mode has been turned on"

# Run any necessary database updates.
drush -y updb

# Import any configuration changes. This should be run after database updates.
drush -y config:import
