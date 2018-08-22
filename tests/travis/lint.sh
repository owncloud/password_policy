#!/usr/bin/env bash

set -e

find . -name \*.php -not -path './vendor/*' -exec bash -c 'php -l "{}" || echo "{}">>lint-errors.txt' \;
EXIT_STATUS=$?
if [ ${EXIT_STATUS} -ne 0 ]
then
	echo "Error in find in lint.sh script"
	exit ${EXIT_STATUS}
fi

if [ -e lint-errors.txt ]
then
	echo "**** Lint errors were detected in the following files:"
	cat lint-errors.txt
	rm lint-errors.txt
	exit 1
else
	exit 0
fi
