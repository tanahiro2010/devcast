# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.4.0] - 2026-08-11

CLI, matching the "v1.4 — CLI" milestone of the project's internal roadmap.

### Added

- `bin/router-dsl` executable with two commands: `routes` (lists routes, supports `--json`,
  `--method=`, `--name=`) and `validate` (runs `Routes::validate()`, exit code 1 on failure).
  Both support `--bootstrap=path` to load a PHP file that must `return` a `Routes` instance
  (defaults to `./routes.php`). Neither command instantiates a Slim `App` — only
  `compile()`/`dump()`/`toArray()`/`validate()` are used, so routes can be listed without
  booting a full DI container.
- Hand-rolled CLI dispatcher (`Cli\Application`, `Cli\Commands\RoutesCommand`,
  `Cli\Commands\ValidateCommand`) — no symfony/console dependency added, matching the
  project's existing minimal-dependency stance.
- `composer.json` now declares `bin/router-dsl` via the `bin` key.

### Internal

- Extracted `Routes::dump()`/`toArray()`'s formatting logic into a new `Compiler\RouteDumper`
  collaborator operating on `CompiledRoute[]`, so the CLI can format arbitrary (e.g.
  method/name-filtered) route lists using the exact same logic. Pure refactor — `Routes`'
  public API and output are unchanged.

## [1.3.0] - 2026-08-11

DSL Convenience, matching the "v1.3 — DSL Convenience" milestone of the project's internal
roadmap.

### Added

- `Route::resource(string $prefix, mixed $controller, ?array $only = null, ?array $except = null): RouteGroup`
  — generates the standard `index/show/create/update/patch/delete` CRUD routes. `$only` and
  `$except` are mutually exclusive (`InvalidRouteException` if both are given).
- `Route::controller(mixed $controller, array $children): ControllerGroup` — new `ControllerGroup`
  node. Inside it, a plain string `HttpRoute` handler is resolved as `[$controller, $handler]`
  at flatten time via a new `RouteContext::$controller`/`withController()`. String handlers
  outside a `Route::controller()` block are completely unaffected (ambient controller is `null`),
  so this is purely additive with no behavior change for existing route trees.

### Changed

- None (no breaking changes in this release).

## [1.2.0] - 2026-08-11

Metadata, matching the "v1.2 — Metadata" milestone of the project's internal roadmap.

### Added

- `HttpRoute::meta(array): self` — fluent, immutable route-level metadata (merges over
  existing metadata, later keys override earlier ones). Group/Middleware-level metadata
  inheritance is intentionally out of scope for this release.
- `CompiledRoute::$metadata` — populated from `HttpRoute::$metadata` by `RouteFlattener`
- `Routes::filter(callable $predicate): CompiledRoute[]` — query routes by any predicate,
  e.g. `$routes->filter(fn ($r) => $r->metadata['auth'] ?? false)`

### Changed

- **Breaking:** `Routes::toArray()` now includes a `'metadata'` key on every row, for
  consistency with `compile()`'s `CompiledRoute::$metadata`.

## [1.1.0] - 2026-08-11

Inspection & Validation, matching the "v1.1 — Inspection & Validation" milestone of the
project's internal roadmap.

### Added

- `Routes::compile()` — public `CompiledRoute[]` API, the shared foundation for `toArray()`,
  `dump()`, and the new inspection methods below
- `Routes::findByName()`, `filterByMethod()`, `findByPath()`, `filterByMiddleware()` — backed
  by a new internal `RouteInspector` collaborator
- `Routes::validate()` — detects duplicate method+path definitions (`DuplicateRouteException`)
  and duplicate route names (`DuplicateRouteNameException`), backed by a new internal
  `RouteValidator` collaborator. Opt-in only; not called automatically by `deploy()`
- New exceptions: `DuplicateRouteException`, `DuplicateRouteNameException`

### Changed

- **Breaking:** `dump()` output is now a column-aligned table (`METHOD`/`PATH`/`NAME`/
  `MIDDLEWARE`, optionally `HANDLER`) instead of the previous two-column `"METHOD  PATH"`
  format. New signature: `dump(bool $showMiddleware = true, bool $showName = true, bool $showHandler = false): string`

## [1.0.0] - 2026-08-11

Initial release. Scope matches the "v1.0.0 — Initial Release" milestone of the project's
internal roadmap.

### Added

- `Routes` / `Routes::deploy()` / `Routes::toArray()` / `Routes::dump()`
- HTTP Method DSL: `Route::get/post/put/patch/delete/options/head/map/any()`
- `Route::group()` with nested-group path normalization
- `Route::middleware()` supporting both array form (`[A::class, B::class]`) and nested form
- Route-level fluent middleware and name: `HttpRoute::middleware()` / `HttpRoute::name()`
- Immutable `RouteNode` tree (`HttpRoute`, `RouteGroup`, `MiddlewareGroup`)
- `RouteFlattener` / `CompiledRoute` — Slim-independent resolution of the route tree
- `SlimRouteCompiler` — deploys a flattened route tree to Slim 4, preserving declared
  middleware order despite Slim's LIFO `add()`
- Exception hierarchy: `RouterDslException`, `InvalidRouteNodeException`,
  `InvalidRouteException`, `InvalidMiddlewareException`
- PHPUnit test suite (Unit + Deploy)
- README
