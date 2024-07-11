install-libs:
	composer install

setup: install-libs db

db:
	./console app:db

import-estate:
	./console app:import-estate -v

import-estate-update:
	./console app:import-estate -v estate_update.xlsx