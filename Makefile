.PHONY: front back lp migrate

db:
	cd database && docker compose up

migrate:
	cd backend && php src/database/migrate.php

front:
	cd frontend && bun run dev

back:
	cd backend && php -S localhost:8000 -t . index.php

lp:
	cd lp && bun run dev
