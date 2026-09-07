# DevCast API docs (TypeSpec)

`backend/` has no schema library (no zod / class-validator equivalent), so this
spec was hand-authored by reading the live Slim Framework controllers under
`backend/src/Futures` and the route table at `backend/src/Config/routes.php`.
Treat it as the source of truth for the HTTP contract going forward.

## Layout

- `main.tsp` — service info, servers, catch-all 404
- `common.tsp` — shared success/error envelope (`ApiSuccess<T>` / `ApiError`), mirrors `App\Models\Response\*`
- `models.tsp` — domain models (`Profile`, `Provider`, `Subscription`, `Article`, `ArticlesMetadata`)
- `auth.tsp` — `/health`, `/auth`, `/auth/token/*`, `/auth/profile`, `/auth/callback`
- `v1.tsp` — `/v1`, `/v1/providers` (CRUD), `/v1/articles`

## Usage

```bash
bun install
bun run build       # compiles to tsp-output/openapi/devcast-api.yaml
bun run watch       # recompile on change
bun run build:html  # renders dist/index.html + dist/openapi.yaml from the compiled spec
bun run build:all   # build + build:html
```

## CI/CD

`.github/workflows/deploy.yml` has an `ApiDocs` job that runs `bun run
build:all` on any push to `main` touching `backend/docs/**` (or via manual
`workflow_dispatch` with target `docs`/`all`), then FTP-deploys `dist/` to
`docs.devcast.work/api/`. Update the `.tsp` files whenever the backend
contract changes so the published docs stay accurate — nothing generates
them automatically from the PHP source.

## Known gaps / follow-ups

- `Article.tags` is documented as a JSON-encoded string, not a `string[]`,
  because `Article::toArray()` returns the raw `jsonb` column value
  un-decoded (see `backend/src/Models/DB/Article.php`). Fix on the backend
  if this should be a real array in the response.
- Article update/delete endpoints (`PUT`/`DELETE /v1/articles/{id}`) and
  publish-status endpoints aren't implemented yet — only list and create
  exist today.
