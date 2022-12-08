#!/bin/bash

while true; do
  echo "=> Building changes"
  drush omnipedia:changes-build --verbose || true;
  echo "=> Sleeping for 5 minutes"
  sleep 300;
done
