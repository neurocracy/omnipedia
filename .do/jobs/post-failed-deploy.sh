#!/bin/bash

# Disable maintenance mode.
#
# The database is shared between all deploys, even failed ones, so we need to
# turn off maintenance mode so that the live copy of the site (not this failed
# deploy) is publicly accessible again.
drush maint:set 0
echo "=> Maintenance mode has been turned off"
