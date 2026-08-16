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
	cd frontend && bun run dev

back:
	docker compose up --build backend

lp:
	cd lp && bun run dev
