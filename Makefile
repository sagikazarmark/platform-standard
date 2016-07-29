PHP?=php
PHPOPTS=
PHPCMD=$(PHP) $(PHPOPTS)
CONSOLE?=bin/console
CONSOLEOPTS=
CONSOLECMD=$(PHPCMD) $(CONSOLE) $(CONSOLEOPTS)

COMPOSER:=$(shell if which composer > /dev/null 2>&1; then which composer; fi)
COMPOSEROPTS=

help:
	@echo 'Makefile for a Symfony application               '
	@echo '                                                 '
	@echo 'Usage:                                           '
	@echo '    make clear  clear the cache                  '
	@echo '    make deps   install project dependencies     '
	@echo '    make setup  setup project for development    '
	@echo '    make test   execute test suite               '
	@echo '                set COVERAGE=true to run coverage'
	@echo '                                                 '

clear:
	$(CONSOLECMD) cache:clear

deps:
ifdef COMPOSER
	$(COMPOSER) install $(COMPOSEROPTS)
endif

setup:
	$(CONSOLECMD) oro:install --organization-name Oro --user-name admin --user-email admin@example.com --user-firstname John --user-lastname Doe --user-password admin --sample-data n --application-url http://local.orocrm.com --force
	$(CONSOLECMD) hotfix:doctrine:database:create --if-not-exists --env test
	$(CONSOLECMD) oro:install --env test --organization-name Oro --user-name admin --user-email admin@example.com --user-firstname John --user-lastname Doe --user-password admin --sample-data n --application-url http://localhost --force
	$(CONSOLECMD) doctrine:fixture:load --no-debug --append --no-interaction --env=test --fixtures ./vendor/oro/platform/src/Oro/Bundle/TestFrameworkBundle/Fixtures
	$(CONSOLECMD) doctrine:schema:update --force --env test

test:
ifeq ($(COVERAGE), true)
	vendor/bin/phpspec run -c phpspec.ci.yml run
	vendor/bin/phpunit --coverage-text --coverage-clover build/coverage.clover
else
	vendor/bin/phpspec run
	vendor/bin/phpunit
endif
	vendor/bin/behat

.PHONY: help clear deps setup test
