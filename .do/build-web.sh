#!/bin/bash

# Build the web service.

.do/build/composer.sh

.do/build/yarn.sh

.do/build/secure-filesystem.sh
