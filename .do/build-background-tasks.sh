#!/bin/bash

source "${BASH_SOURCE%/*}/build/composer.sh"

# Secure the file system by removing write permissions.
#
# @see https://stackoverflow.com/questions/6659689/referring-to-a-file-relative-to-executing-script#6659698
source "${BASH_SOURCE%/*}/build/secure-filesystem.sh"
