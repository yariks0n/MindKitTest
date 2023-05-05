RED=\033[0;31m
GREEN=\033[1;92m
NC=\033[0m # No Color

up:
	docker-compose up -d

prepare-env: env-files db-prepare fixtures jwt

env-files:
	cp .env.dist .env
	cp .env.test.dist .env.test

db-prepare: env-files
	docker-compose exec app php bin/console doctrine:migrations:migrate --no-interaction || (docker-compose logs --tail=10 app && @echo "${RED}Composer install is already running!\nPlease wait while the symfony core is installed${NC}\n" && false)

fixtures: db-prepare
	docker-compose exec app php bin/console doctrine:fixtures:load --no-interaction

jwt:
	php bin/console lexik:jwt:generate-keypair --overwrite --no-interaction
	@printf "$(GREEN)The Web server is ready http://127.0.0.1/api\n"

unit-tests:
	php bin/phpunit tests/Unit

api-tests:
	docker-compose exec app php bin/phpunit tests/Api

phpcs:
	./vendor/bin/phpcs

phpstan:
	./vendor/bin/phpstan analyse -c phpstan.neon

down:
	docker-compose down
	rm -rf var
	rm -rf docker/data
	rm -rf build

