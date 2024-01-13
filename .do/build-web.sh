#!/bin/bash

# Build the web service.

source "${BASH_SOURCE%/*}/build/composer.sh"

source "${BASH_SOURCE%/*}/build/yarn.sh"

# @see https://stackoverflow.com/questions/6659689/referring-to-a-file-relative-to-executing-script#6659698
source "${BASH_SOURCE%/*}/secure-filesystem.sh"
