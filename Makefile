SHELL := /bin/bash

NODE_PREFIX=$(shell pwd)
BOWER=$(NODE_PREFIX)/node_modules/bower/bin/bower
JSDOC=$(NODE_PREFIX)/node_modules/.bin/jsdoc

# dependency folders (leave empty if not required)
composer_deps=
composer_dev_deps=
nodejs_deps=
bower_deps=

include ../../build/rules/help.mk
include ../../build/rules/check-npm.mk
include ../../build/rules/dist.mk
include ../../build/rules/test-all.mk
include ../../build/rules/clean.mk

#
# Node dependencies
#
$(nodejs_deps): package.json
	$(NPM) install --prefix $(NODE_PREFIX) && touch $@

$(BOWER): $(nodejs_deps)
$(JSDOC): $(nodejs_deps)

$(bower_deps): $(BOWER)
	$(BOWER) install && touch $@

clean_deps_rules+=clean-js-deps

.PHONY: clean-js-deps
clean-js-deps:
	rm -Rf $(nodejs_deps) $(bower_deps)

