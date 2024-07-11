install-libs:
	composer install

setup: install-libs db import-estate

db:
	./console app:db -v

import-estate:
	./console app:import-estate -v

import-estate-update:
	./console app:import-estate -v estate_update.xlsx

linter:
	./vendor/bin/php-cs-fixer fix .

test:
	# Тесты не написаны

lint: linter

.PHONY: test