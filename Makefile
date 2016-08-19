PHP?=php
PHPOPTS=
PHPCMD=$(PHP) $(PHPOPTS)
CONSOLE?=bin/console
CONSOLEOPTS=
CONSOLECMD=$(PHPCMD) $(CONSOLE) $(CONSOLEOPTS)

COMPOSER:=$(shell if which composer > /dev/null 2>&1; then which composer; fi)
COMPOSEROPTS=

help:
	@echo 'Makefile for a Symfony application                                      '
	@echo '                                                                        '
	@echo 'Usage:                                                                  '
	@echo '    make clear     clear the cache                                      '
	@echo '    make deps      install project dependencies                         '
	@echo '    make setup     setup project for development                        '
	@echo '                   set TEST=true update schema instead of migrations    '
	@echo '                   Note: SYMFONY_ENV=test env var SHOULD BE set manually'
	@echo '    make test      execute test suite                                   '
	@echo '                   set COVERAGE=true to run coverage                    '
	@echo '                                                                        '

clear:
	$(CONSOLECMD) cache:clear

deps:
ifdef COMPOSER
	$(COMPOSER) install $(COMPOSEROPTS)
endif

setup:
	$(CONSOLECMD) hotfix:doctrine:database:create --if-not-exists
	$(CONSOLECMD) oro:install --organization-name Oro --user-name admin --user-email admin@example.com --user-firstname John --user-lastname Doe --user-password admin --sample-data n --application-url http://local.orocrm.com --force
ifeq ($(TEST), true)
	$(CONSOLECMD) doctrine:fixture:load --no-debug --append --no-interaction --env=test --fixtures ./vendor/oro/platform/src/Oro/Bundle/TestFrameworkBundle/Fixtures
endif

test:
	$(CONSOLECMD) lint:yaml -v --ansi app
ifeq ($(COVERAGE), true)
	vendor/bin/phpspec run -c phpspec.ci.yml
	vendor/bin/phpunit --coverage-text --coverage-clover build/coverage.clover
else
	vendor/bin/phpspec run
	vendor/bin/phpunit
endif
	vendor/bin/behat

.PHONY: help clear deps setup test
