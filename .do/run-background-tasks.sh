#!/bin/bash

# This runs various background tasks in sequence. These are mostly in order of
# importance and order of dependency (e.g. entity warmer before wiki page
# changes), though not strictly required to be so.
#
# @todo Implement some way to run cron on a more predictable schedule, possibly
#   via parallel execution?

# Rebuild asset libraries as a temporary workaround for local aggregation.
#
# Since App Platform wipes the filesystem on every deploy, locally saved
# aggregated assets get wiped as well, and neither Drupal nor AdvAgg clue in
# that the files they'd previously aggregated no longer exist, which results in
# a 404. This is a temporary workaround to invalidate all the aggregated assets
# and cause them to be rebuilt so they exist in the new filesystem after a
# deploy.
#
# @todo Implement backing up aggregated assets to DigitalOcean Spaces when they
#   get created and attempt to pull them in after a deploy, only rebuilding
#   assets if any of them are not found.
drush rebuilder asset

while true; do

  echo "=> Running cron"
  drush cron || true;
  echo "=> Cron run completed"

  echo "=> Running image style warmer"
  drush queue:run image_style_warmer_pregenerator --verbose || true;
  echo "=> Image style warmer run completed"

  echo "=> Running wiki page CDN warmer"
  drush warmer:enqueue omnipedia_wiki_node_cdn --run-queue --verbose || true;
  echo "=> Wiki page CDN warmer run completed"

  echo "=> Building wiki page changes"
  drush omnipedia:changes-build --verbose || true;
  echo "=> Wiki page changes build completed"

  echo "=> Sleeping for 5 minutes"
  sleep 300;

done
