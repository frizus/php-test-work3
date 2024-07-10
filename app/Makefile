install-libs:
	composer install

setup: install-libs

db:
	./console app:db

import:
	./console app:import-estate

update-import:
	./console app:import-estate estate_update.xlsx

import-update: update-import