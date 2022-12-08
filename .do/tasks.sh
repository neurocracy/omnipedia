#!/bin/bash

# This runs various background tasks in sequence. These are mostly in order of
# importance and order of dependency (e.g. entity warmer before wiki page
# changes), though not strictly required to be so.
#
# @todo Implement some way to run cron on a more predictable schedule, possibly
#   via parallel execution?

while true; do

  echo "=> Running cron"
  drush cron || true;
  echo "=> Cron run completed"

  echo "=> Running image style warmer"
  drush queue:run image_style_warmer_pregenerator --verbose || true;
  echo "=> Image style warmer run completed"

  echo "=> Running entity warmer"
  drush warmer:enqueue entity --run-queue --verbose || true;
  echo "=> Entity warmer run completed"

  echo "=> Building wiki page changes"
  drush omnipedia:changes-build --verbose || true;
  echo "=> Wiki page changes build completed"

  echo "=> Sleeping for 5 minutes"
  sleep 300;

done
