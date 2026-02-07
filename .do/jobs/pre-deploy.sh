#!/bin/bash

drush state:set digitalocean.deployment_active true

if [ "$(drush maint:get)" == "0" ]; then

  # Enable maintenance mode.
  drush maint:set 1
  echo "=> Maintenance mode has been turned on"

  drush state:set digitalocean.maint_preserve_on 0

else

  echo "=> Maintenance mode is already turned on; leaving as-is"

  drush state:set digitalocean.maint_preserve_on 1

fi

# Run any necessary database updates.
drush -y updb

# Import any configuration changes. This should be run after database updates.
drush -y config:import
