# DevCast API docs (TypeSpec)

`backend/` has no schema library (no zod / class-validator equivalent), so this
spec was hand-authored by reading the live Slim Framework controllers under
`backend/src/Futures` and the route table at `backend/src/Config/routes.php`.
Treat it as the source of truth for the HTTP contract going forward.

## Layout

- `main.tsp` — service info, servers, catch-all 404
- `common.tsp` — shared success/error envelope (`ApiSuccess<T>` / `ApiError`), mirrors `App\Models\Response\*`
- `models.tsp` — domain models (`Profile`, `Provider`, `Subscription`)
- `auth.tsp` — `/health`, `/auth`, `/auth/token/*`, `/auth/profile`, `/auth/callback`
- `v1.tsp` — `/v1`, `/v1/providers`

## Usage

```bash
bun install
bun run build       # compiles to tsp-output/openapi/devcast-api.yaml
bun run watch       # recompile on change
bun run build:html  # renders a static Redoc HTML page from the YAML
bun run build:all   # build + build:html
```

## Known gaps / follow-ups

- `POST /v1/providers` (`ProvidersController::registerProvider`) is
  implemented but not yet registered in `routes.php` — documented ahead of
  wiring so the contract is agreed before it ships.
