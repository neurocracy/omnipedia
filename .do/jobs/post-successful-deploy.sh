#!/bin/bash

# Disable maintenance mode if not already enabled when deployment started.
if [ "$(drush state:get digitalocean.maint_preserve_on)" == "1" ]; then

  echo "=> Maintenance mode was already turned on before deployment; leaving it enabled"

else

  drush maint:set 0
  echo "=> Maintenance mode has been turned off"

fi

drush state:set digitalocean.deployment_active false
