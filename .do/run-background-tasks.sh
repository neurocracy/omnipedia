#!/bin/bash

# @see https://stackoverflow.com/questions/6659689/referring-to-a-file-relative-to-executing-script#6659698
source "${BASH_SOURCE%/*}/run-common.sh"

# This runs various background tasks in sequence. These are mostly in order of
# importance and order of dependency (e.g. entity warmer before wiki page
# changes), though not strictly required to be so.
#
# @todo Implement some way to run cron on a more predictable schedule, possibly
#   via parallel execution?

# This adds an initial delay before doing anything.
#
# This is to give the web component a little bit of lead time when deploying.
#
# @todo Implement a better way of doing this, ideally waiting for a signal from
#   the web component.
echo "Sleeping for 5 seconds to allow the web component to start."
sleep 5;

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
