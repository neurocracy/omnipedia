#!/bin/bash

# Enable maintenance mode.
drush maint:set 1
echo "=> Maintenance mode has been turned on"

# Run any necessary database updates.
drush -y updb
