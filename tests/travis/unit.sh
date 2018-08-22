#!/usr/bin/env bash

set -e

# Run phpunit tests
cd tests/unit
../../../../lib/composer/bin/phpunit --configuration phpunit.xml
