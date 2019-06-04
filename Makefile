.DEFAULT_GOAL := help

help:
	@echo ""
	@echo "Available tasks:"
	@echo "    test    Run all tests and generate coverage"
	@echo "    watch   Run all tests and coverage when a source file is upaded"
	@echo "    lint    Run only linter and code style checker"
	@echo "    unit    Run unit tests and generate coverage"
	@echo "    unit    Run static analysis"
	@echo "    vendor  Install dependencies"
	@echo "    clean   Remove vendor and composer.lock"
	@echo ""

vendor: $(wildcard composer.lock)
	composer install --prefer-dist

lint: vendor
	vendor/bin/phplint . --exclude=vendor/
	vendor/bin/phpcs -p --standard=PSR2 --exclude=PSR2.Namespaces.UseDeclaration --extensions=php --encoding=utf-8 --ignore=*/vendor/*,*/benchmarks/*,*/db/* .

unit: vendor
	phpdbg -qrr vendor/bin/phpunit --coverage-text --coverage-clover=coverage.xml --coverage-html=./report/

watch: vendor
	find . -name "*.php" -not -path "./vendor/*" -o -name "*.json" -not -path "./vendor/*" | entr -c make test

test: lint unit

travis: lint unit

clean:
	rm -rf vendor
	rm composer.lock
	rm .phplint-cache
	rm -rf report

.PHONY: help lint unit watch test travis clean
