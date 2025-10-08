#!/bin/bash

# @see https://superuser.com/questions/270214/how-can-i-change-the-colors-of-my-xterm-using-ansi-escape-sequences
RED='\033[31m'
GREEN='\033[32m'
YELLOW='\033[33m'
BLUE='\033[34m'
MAGENTA='\033[35m'
CYAN='\033[36m'
RESET='\033[0m'

if [[ -z "${DRUPAL_CRON_KEY}" ]]; then

  printf "${RED}The DRUPAL_CRON_KEY environment variable is not set!${RESET}\n" >&2

  exit 1

fi

# @see https://superuser.com/questions/272265/getting-curl-to-output-http-status-code/442395#442395
RESPONSE_CODE=$(curl -s -o /dev/null -w "%{http_code}" "https://${DRUPAL_PRIMARY_HOST}/cron/${DRUPAL_CRON_KEY}")

if ((RESPONSE_CODE >= 200 && RESPONSE_CODE < 300)); then

  printf "${GREEN}cron invoked successfully; response code: $RESPONSE_CODE${RESET}\n"

  exit 0

fi

printf "${RED}cron invoked unsuccessfully; response code: $RESPONSE_CODE\n" >&2

exit 1
