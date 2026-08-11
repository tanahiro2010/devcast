.PHONY: front back lp migrate make-migration

docker:
	cd database && docker compose up -d

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
