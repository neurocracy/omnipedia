#!/bin/bash

# Disable maintenance mode if it wasn't enabled when the deployment started.
#
# The database is shared between all deploys, even failed ones, so we need to
# turn off maintenance mode so that the live copy of the site (not this failed
# deploy) is publicly accessible again.
if [ "$(drush state:get digitalocean.maint_preserve_on)" == "1" ]; then

  echo "=> Maintenance mode was already turned on before deployment; leaving it enabled"

else

  drush maint:set 0
  echo "=> Maintenance mode has been turned off"

fi

drush state:set digitalocean.deployment_active false
