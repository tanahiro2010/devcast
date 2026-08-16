.PHONY: front back lp migrate make-migration docker docker-reset

docker:
	docker compose up -d

docker-reset:
	docker compose down -v

migrate:
	cd backend && php scripts/migrate.php

make-migration:
	cd backend && php scripts/make_migration.php $(name)

front:
	docker compose up --build frontend

back:
	docker compose up --build backend

lp:
	docker compose up --build lp
