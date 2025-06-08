install: build up composer migrate

build:
	COMPOSE_BAKE=true docker compose build

up:
	docker compose up -d

stop:
	docker compose stop

composer:
	docker compose exec app composer install --no-interaction --prefer-dist --optimize-autoloader

migrate:
	docker compose exec app php bin/console doctrine:migrations:migrate --no-interaction

logs:
	docker compose logs -f --tail=100

shell:
	docker compose exec app sh

restart:
	docker compose restart

down:
	docker compose down --volumes --remove-orphans
