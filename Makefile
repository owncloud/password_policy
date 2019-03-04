SHELL := /bin/bash

include ../../build/rules/help.mk
include ../../build/rules/check-npm.mk
include ../../build/rules/dist.mk
include ../../build/rules/test-all.mk
include ../../build/rules/clean.mk
