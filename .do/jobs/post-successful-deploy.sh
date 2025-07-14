#!/bin/bash

# Disable maintenance mode.
# drush maint:set 0
# echo "=> Maintenance mode has been turned off"

drush state:set digitalocean.deployment_active false
