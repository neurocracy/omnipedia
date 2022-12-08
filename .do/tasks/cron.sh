#!/bin/bash

while true; do
  echo "=> Running cron"
  drush cron || true;
  echo "=> Sleeping for 5 minutes"
  sleep 300;
done
