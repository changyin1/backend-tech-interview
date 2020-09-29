.PHONY: init down build up install migrate refresh phpunit bash cache
default: init

init: down build up install migrate

down:
	docker-compose down
build:
	docker-compose build
up:
	docker-compose up -d

install:
	docker-compose run --rm --volume `pwd`:/app --volume `pwd`/.composer:/.composer --user $(id -u):$(id -g) --workdir /app php-fpm composer --prefer-dist install

migrate:
	docker-compose run --rm --volume `pwd`:/app --workdir /app php-fpm php artisan migrate

refresh:
	docker-compose run --rm --volume `pwd`:/app --workdir /app php-fpm php artisan migrate:refresh

test:
	docker-compose run --rm --volume `pwd`:/app --workdir /app php-fpm php artisan config:clear
	docker-compose run --rm --volume `pwd`:/app --workdir /app php-fpm php artisan cache:clear
	docker-compose run --rm --volume `pwd`:/app  --workdir /app php-fpm vendor/bin/phpunit

phpunit: test

bash:
	docker-compose run --rm --volume `pwd`:/app --workdir /app php-fpm bash

cache:
	docker-compose run --rm --volume `pwd`:/app --workdir /app php-fpm php artisan cache:clear
