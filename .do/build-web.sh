#!/bin/bash

# Build the web service.

.do/build/composer.sh && \
.do/build/harden-packages.sh && \
.do/build/yarn.sh && \
.do/build/secure-filesystem.sh

# Explicitly exit here using the most recent exit code from the above. Note the
# need to chain them all using the && operator to ensure an error in one stops
# execution immediately.
exit $?
