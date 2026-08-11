.PHONY: front back lp

db:
  cd database && docker compose up

front:
	cd frontend && bun run dev

back:
	cd backend && php -S localhost:8000 -t . index.php

lp:
	cd lp && bun run dev
