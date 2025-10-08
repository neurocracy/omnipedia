FROM alpine/curl

ARG DRUPAL_CRON_KEY
ARG DRUPAL_PRIMARY_HOST

COPY cron.sh /

RUN apk add --no-cache bash

CMD ['/bin/bash', '/cron.sh']
