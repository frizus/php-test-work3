run:
	docker-compose up -d

copy-env:
	cp -n .env.default .env || true

build: copy-env
	docker-compose build

build-no-cache: copy-env
	docker-compose build --no-cache

down:
	docker-compose stop || true

clear:
	docker-compose down -v --remove-orphans || true

build-php:
	docker-compose build php

rebuild-php:
	docker-compose build --no-cache php

build-nginx:
	docker-compose build nginx

rebuild-nginx:
	docker-compose build --no-cache nginx

build-db:
	docker-compose build db

rebuild-db:
	docker-compose build --no-cache db

start: run

launch: run

compose: run

up: run

rebuild: build-no-cache