#!/bin/bash

# Build the background tasks worker.
#
# @see https://stackoverflow.com/questions/6659689/referring-to-a-file-relative-to-executing-script#6659698
#   Describes how to include a Bash script relative to the current path.

source "${BASH_SOURCE%/*}/build/composer.sh"

source "${BASH_SOURCE%/*}/build/secure-filesystem.sh"
