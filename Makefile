.PHONY: front back lp migrate make-migration docker docker-reset

docker:
	cd database && docker compose up -d

docker-reset:
	cd database && docker compose down -v

migrate:
	cd backend && php scripts/migrate.php

make-migration:
	cd backend && php scripts/make_migration.php $(name)

front:
	cd frontend && bun run dev

back:
	cd backend && php -S localhost:8000 -t . index.php

lp:
	cd lp && bun run dev
