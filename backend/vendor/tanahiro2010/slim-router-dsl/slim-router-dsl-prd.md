# Slim Router DSL — Product Requirements Document

## 1. 概要

### 1.1 プロダクト名

仮称: **Slim Router DSL**

### 1.2 目的

Slim Framework のルーティング定義を、命令的な登録処理ではなく、**宣言的なルートツリーとして記述できるDSL**として提供する。

Slim標準のルーティングAPIを置き換えること自体を目的にはせず、Slimの上に薄い抽象化レイヤーを設けることで、以下を実現する。

- ルート構造をコード上で視覚的に把握しやすくする
- Route Group / Middleware / HTTP Route を同一のツリー構造として扱う
- ルート定義とSlimへの登録処理を分離する
- 将来的なルート一覧出力、解析、OpenAPI生成などの拡張余地を確保する
- Slim固有APIへの過度な依存を避けつつ、Slimとの親和性を維持する

---

# 2. 背景

Slim Framework では通常、以下のような形でルートを登録する。

```php
$app->group('/api', function (RouteCollectorProxy $group) {
    $group->get('/users', [UserController::class, 'index']);

    $group->group('/posts', function (RouteCollectorProxy $group) {
        $group->get('', [PostController::class, 'index']);
        $group->get('/{id}', [PostController::class, 'show']);
    });
})->add(AuthMiddleware::class);
```

この形式はSlimのAPIとして自然である一方、ルート数やネストが増えた場合に以下の課題がある。

- ルート構造と登録処理が混在する
- Closure のネストが深くなりやすい
- Middleware の適用範囲を視覚的に把握しにくい
- ルート定義をSlimへ登録する前に解析・加工しにくい
- ルーティング情報を再利用しにくい

Slim Router DSLでは、ルーティングをまず**データ構造として定義**し、その後 `deploy()` によってSlimへ登録する。

---

# 3. 設計思想

## 3.1 Declarative First

ルーティングは「登録処理」ではなく「ルート構造の定義」として記述する。

```php
$routes = new Routes([
    Route::group('/api', [
        Route::get('/users', [UserController::class, 'index']),
        Route::post('/users', [UserController::class, 'create']),
    ]),
]);

$routes->deploy($app);
```

---

## 3.2 Route Tree

すべてのルート定義を木構造として扱う。

```text
Routes
└── Group /api
    ├── GET /users
    ├── POST /users
    └── Middleware AuthMiddleware
        └── Group /posts
            ├── GET /
            └── GET /{id}
```

Group はパスを子ノードへ継承する。

Middleware はMiddleware情報を子ノードへ継承する。

---

## 3.3 Define First, Deploy Later

`Route::*()` を呼び出した時点ではSlimへルート登録を行わない。

```php
$routes = new Routes([...]);
```

この時点ではルートツリーをメモリ上に構築するだけとする。

```php
$routes->deploy($app);
```

を実行した時点で初めてSlimへ登録する。

---

## 3.4 Slimを隠しすぎない

DSLはSlimの代替フレームワークにはしない。

以下の責務は原則Slimまたは利用者側に委ねる。

- Dependency Injection
- Controllerの生成
- PSR-7 Request / Response
- PSR-15 Middleware
- Error Handling
- Container
- Application lifecycle

DSLは基本的に**ルート定義と展開のみ**を担当する。

---

## 3.5 Minimal Magic

挙動がコードから推測できない暗黙的な処理は極力避ける。

特にv1では以下を行わない。

- Controllerメソッドの自動探索
- HTTP Methodの自動推論
- Reflectionベースの自動Route生成
- Attribute/Annotationからの自動登録
- 命名規則による暗黙的なパス生成

---

# 4. ターゲットユーザー

主な対象は以下。

- Slim Frameworkを使用しているPHP開発者
- 小〜中規模APIを開発するユーザー
- ルーティング定義を宣言的に整理したいユーザー
- routes.php の可読性を重視するユーザー
- 独自フレームワークまでは不要だがSlimのルーティングを整理したいユーザー

---

# 5. 基本API

## 5.1 Routes

`Routes` はDSL全体のルートノードを保持するルートコンテナとする。

```php
$routes = new Routes([
    Route::get('/', HomeController::class),
    Route::group('/api', [
        // ...
    ]),
]);
```

### Constructor

```php
new Routes(array $routes)
```

引数には `RouteNode` の配列を受け取る。

---

## 5.2 deploy()

Slim Applicationへルートを展開する。

```php
$routes->deploy($app);
```

### 想定シグネチャ

```php
public function deploy(App $app): void;
```

v1ではSlim 4専用とする。

---

# 6. Route DSL

`Route` はルートツリーを構築するためのDSL Factoryとして扱う。

`Route` 自体を「1つのHTTP Route」とは定義せず、各種RouteNodeを生成する入口とする。

---

# 7. HTTP Route

## 7.1 GET

```php
Route::get('/users', [UserController::class, 'index']);
```

---

## 7.2 POST

```php
Route::post('/users', [UserController::class, 'create']);
```

---

## 7.3 PUT

```php
Route::put('/users/{id}', [UserController::class, 'update']);
```

---

## 7.4 PATCH

```php
Route::patch('/users/{id}', [UserController::class, 'patch']);
```

---

## 7.5 DELETE

```php
Route::delete('/users/{id}', [UserController::class, 'delete']);
```

---

## 7.6 OPTIONS

```php
Route::options('/users', [UserController::class, 'options']);
```

---

## 7.7 ANY

複数の主要HTTP Methodを受け付けるルートを定義できる。

```php
Route::any('/health', HealthController::class);
```

具体的に展開するMethod一覧は実装時に定数として管理する。

---

## 7.8 MAP

任意のHTTP Methodを指定できる。

```php
Route::map(
    ['GET', 'HEAD'],
    '/resource',
    ResourceController::class,
);
```

---

# 8. Handler仕様

v1ではSlimが受理可能なHandlerとの互換性を優先する。

最低限、以下を許可する。

## 8.1 Callable

```php
Route::get('/', function ($request, $response) {
    return $response;
});
```

---

## 8.2 Controller + Method

```php
Route::get(
    '/users',
    [UserController::class, 'index'],
);
```

---

## 8.3 Invokable Class

```php
Route::get(
    '/health',
    HealthController::class,
);
```

Controllerの解決方法はSlim / Containerに委ねる。

DSL自身はControllerをnewしない。

---

# 9. Group

パスPrefixを複数ルートへ適用する。

```php
Route::group('/api', [
    Route::get('/users', ...),
    Route::get('/posts', ...),
]);
```

展開結果:

```text
GET /api/users
GET /api/posts
```

---

## 9.1 Nested Group

Groupはネスト可能とする。

```php
Route::group('/api', [
    Route::group('/v1', [
        Route::get('/users', ...),
    ]),
]);
```

展開結果:

```text
GET /api/v1/users
```

---

## 9.2 Path結合

GroupとRouteのパス結合時には `/` の重複を正規化する。

以下はすべて同等に扱う。

```text
/api + /users
/api/ + /users
/api + users
```

結果:

```text
/api/users
```

ただし、ユーザー入力自体を書き換えるのではなく、deploy時またはcompile時に正規化する。

---

# 10. Middleware

Middlewareを複数の子Routeへ適用する。

```php
Route::middleware(AuthMiddleware::class, [
    Route::get('/me', ...),
    Route::get('/settings', ...),
]);
```

---

## 10.1 Middleware + Group

MiddlewareはGroupと自由にネストできる。

```php
Route::group('/api', [
    Route::middleware(AuthMiddleware::class, [
        Route::get('/me', ...),

        Route::group('/posts', [
            Route::get('/', ...),
            Route::post('/', ...),
        ]),
    ]),
]);
```

---

## 10.2 Middlewareの指定形式

v1ではSlimがサポートするMiddleware指定形式との互換性を基本とする。

想定:

```php
Route::middleware(AuthMiddleware::class, [...]);
```

```php
Route::middleware(new AuthMiddleware(), [...]);
```

```php
Route::middleware($callableMiddleware, [...]);
```

---

## 10.3 複数Middleware

v1で以下のどちらを採用するかは実装時のAPIレビューで決定する。

候補A:

```php
Route::middleware([
    AuthMiddleware::class,
    JsonMiddleware::class,
], [
    // routes
]);
```

候補B:

```php
Route::middleware(AuthMiddleware::class, [
    Route::middleware(JsonMiddleware::class, [
        // routes
    ]),
]);
```

推奨は候補A。

理由:

- ネストが不要
- 実利用で頻繁に必要になる
- Route Treeとしての意味も明確

---

# 11. RouteNode

DSL内部ではすべてのノードを共通インターフェースとして扱う。

```php
interface RouteNode
{
}
```

v1で最低限必要な実装:

```text
RouteNode
├── HttpRoute
├── RouteGroup
└── MiddlewareGroup
```

---

# 12. HttpRoute

単一HTTP Routeを表す。

概念モデル:

```php
final readonly class HttpRoute implements RouteNode
{
    public function __construct(
        public array $methods,
        public string $path,
        public mixed $handler,
    ) {}
}
```

---

# 13. RouteGroup

PrefixとChildrenを持つ。

概念モデル:

```php
final readonly class RouteGroup implements RouteNode
{
    /**
     * @param RouteNode[] $children
     */
    public function __construct(
        public string $prefix,
        public array $children,
    ) {}
}
```

---

# 14. MiddlewareGroup

MiddlewareとChildrenを持つ。

概念モデル:

```php
final readonly class MiddlewareGroup implements RouteNode
{
    /**
     * @param RouteNode[] $children
     */
    public function __construct(
        public array $middleware,
        public array $children,
    ) {}
}
```

---

# 15. Immutability

RouteNodeは原則immutableとする。

PHP 8.2以降を最低要件にできる場合は `readonly class` を推奨する。

理由:

- Route定義がdeploy前に変更されることを防ぐ
- ルートツリーの解析が容易
- 副作用の少ないDSLになる
- デバッグしやすい

---

# 16. Deploy Architecture

`Routes` 自身にSlim登録処理をすべて実装しない。

内部Compilerへ委譲する。

概念:

```text
Routes
  ↓
SlimRouteCompiler
  ↓
Slim\App
```

---

## 16.1 Routes

```php
final class Routes
{
    public function __construct(
        private array $routes,
    ) {}

    public function deploy(App $app): void
    {
        $compiler = new SlimRouteCompiler($app);

        $compiler->compile($this->routes);
    }
}
```

---

## 16.2 SlimRouteCompiler

役割:

- Route Treeを再帰的に走査
- Group Prefixの解決
- Middlewareの継承
- HttpRouteのSlimへの登録

概念:

```php
final class SlimRouteCompiler
{
    public function compile(array $routes): void
    {
        // recursive compile
    }
}
```

---

# 17. Compile Context

再帰処理では現在の状態をContextとして保持する。

概念:

```php
final readonly class RouteContext
{
    public function __construct(
        public string $prefix = '',
        public array $middleware = [],
    ) {}
}
```

Route Tree:

```text
Group /api
└── Middleware Auth
    └── GET /users
```

Contextの変化:

```text
prefix=""
middleware=[]

↓

prefix="/api"
middleware=[]

↓

prefix="/api"
middleware=[Auth]

↓

GET /api/users
middleware=[Auth]
```

---

# 18. Middleware適用順序

Middlewareの順序は明示的に仕様化する。

基本方針:

```php
Route::middleware(A::class, [
    Route::middleware(B::class, [
        Route::get('/', ...)
    ])
]);
```

論理上のMiddleware列:

```text
A
B
Route
```

ただしSlimの `add()` がLIFOで評価される点を考慮し、CompilerがSlimへ登録する順序を調整する。

DSL上で記述した順序と実行順序が直感的に一致することを要件とする。

---

# 19. Route Middleware

将来的にはHTTP Route単体へのMiddleware指定も提供する。

候補:

```php
Route::get('/me', ...)
    ->middleware(AuthMiddleware::class);
```

ただしv1ではimmutable設計との整合性を保つため、Fluent APIを導入する場合は必ず新しいInstanceを返す。

```php
public function middleware(mixed $middleware): static
{
    return clone ...
}
```

v1初期リリースでは、実装複雑度を下げるために `Route::middleware()` によるGroup形式のみでもよい。

---

# 20. Route Name

SlimのNamed Routeに対応する。

候補API:

```php
Route::get('/users/{id}', ...)
    ->name('users.show');
```

または:

```php
Route::named(
    'users.show',
    Route::get('/users/{id}', ...)
);
```

推奨はFluent API。

ただし初期v1ではOptional Featureとする。

---

# 21. Full Usage Example

```php
use App\Controller\PostController;
use App\Controller\UserController;
use App\Middleware\AuthMiddleware;
use App\Middleware\JsonMiddleware;
use Slim\Factory\AppFactory;

$app = AppFactory::create();

$routes = new Routes([
    Route::get('/', HomeController::class),

    Route::group('/api', [
        Route::middleware([
            JsonMiddleware::class,
        ], [
            Route::get('/health', HealthController::class),

            Route::middleware([
                AuthMiddleware::class,
            ], [
                Route::group('/users', [
                    Route::get('/', [UserController::class, 'index']),
                    Route::post('/', [UserController::class, 'create']),
                    Route::get('/{id}', [UserController::class, 'show']),
                    Route::put('/{id}', [UserController::class, 'update']),
                    Route::delete('/{id}', [UserController::class, 'delete']),
                ]),

                Route::group('/posts', [
                    Route::get('/', [PostController::class, 'index']),
                    Route::post('/', [PostController::class, 'create']),
                    Route::get('/{id}', [PostController::class, 'show']),
                ]),
            ]),
        ]),
    ]),
]);

$routes->deploy($app);

$app->run();
```

---

# 22. 推奨ディレクトリ構成

```text
src/
├── Routes.php
├── Route.php
│
├── Contracts/
│   └── RouteNode.php
│
├── Nodes/
│   ├── HttpRoute.php
│   ├── RouteGroup.php
│   └── MiddlewareGroup.php
│
├── Compiler/
│   ├── SlimRouteCompiler.php
│   └── RouteContext.php
│
└── Exception/
    ├── InvalidRouteException.php
    ├── InvalidMiddlewareException.php
    └── InvalidRouteNodeException.php
```

---

# 23. Validation

ルートツリーの不正な入力は可能な限り早期に検出する。

---

## 23.1 Invalid Node

```php
new Routes([
    'hello',
]);
```

はエラーとする。

例:

```text
InvalidRouteNodeException
```

---

## 23.2 Invalid HTTP Method

```php
Route::map(['HELLO'], '/', ...);
```

については、Slimが任意Methodを受理できる場合を考慮し、v1では強制的なMethod whitelistを設けない選択肢もある。

ただし空配列は必ずエラーとする。

```php
Route::map([], '/', ...);
```

---

## 23.3 Empty Handler

HandlerがSlimで利用できない値の場合、可能な範囲でdeploy前に検証する。

ただしSlim Containerが解決するHandler形式を壊さないよう、過度なValidationは行わない。

---

## 23.4 Children

Group / Middleware のchildrenは `RouteNode[]` のみを受け付ける。

---

# 24. Exception Policy

ライブラリ独自の例外はすべて共通ベースを持つ。

```php
interface RouterDslException extends Throwable
{
}
```

または:

```php
abstract class RouterDslException extends RuntimeException
{
}
```

利用者が一括catchできる設計を推奨する。

---

# 25. PHP Version

推奨最低バージョン:

```text
PHP >= 8.2
```

理由:

- readonly class
- 型システム
- modern PHP ecosystem
- Slim 4との利用を想定

より広い互換性を優先する場合はPHP 8.1も候補とする。

---

# 26. Slim Version

対象:

```text
Slim Framework 4.x
```

v1ではSlim 3をサポートしない。

---

# 27. Composer

想定インストール:

```bash
composer require tanahiro2010/slim-router-dsl
```

パッケージ名は公開前に最終決定する。

---

# 28. Namespace

候補:

```php
Tanahiro2010\RouterDsl
```

利用例:

```php
use Tanahiro2010\RouterDsl\Route;
use Tanahiro2010\RouterDsl\Routes;
```

---

# 29. Non-Goals — v1でやらないこと

v1では以下を対象外とする。

- 独自DI Container
- Controller Resolver
- Request validation
- Response serialization
- ORM連携
- Authentication
- Authorization
- OpenAPI自動生成
- PHP Attribute Routing
- Annotation Routing
- File-based Routing
- Controllerメソッド自動探索
- Laravel / Symfony対応
- Slim以外のFramework対応
- Auto Discovery
- Dependency Injection自動化
- Cache
- Routerそのものの再実装

Slim Router DSLは**Slim Routerの置き換えではない**。

---

# 30. Future Features

以下はv1以降の候補とする。

---

## 30.1 Route Dump

```php
$routes->dump();
```

例:

```text
GET     /
GET     /api/users
POST    /api/users
GET     /api/users/{id}
DELETE  /api/users/{id}
```

---

## 30.2 toArray()

```php
$routes->toArray();
```

例:

```php
[
    [
        'methods' => ['GET'],
        'path' => '/api/users',
        'handler' => [UserController::class, 'index'],
        'middleware' => [
            AuthMiddleware::class,
        ],
    ],
];
```

---

## 30.3 Inspection API

```php
$routes->routes();
```

```php
$routes->findByName('users.show');
```

---

## 30.4 OpenAPI Integration

Route Treeを解析できるというDSLの特性を利用し、OpenAPI生成機構との統合を検討する。

ただしRequest/Response Schemaの情報がルート定義だけでは不足するため、別メタデータAPIが必要になる。

---

## 30.5 Resource Routes

```php
Route::resource('/users', UserController::class);
```

生成候補:

```text
GET     /users
GET     /users/{id}
POST    /users
PUT     /users/{id}
PATCH   /users/{id}
DELETE  /users/{id}
```

v1では導入しない。

理由:

DSLの基本構造が安定してから追加する方がよい。

---

## 30.6 Controller Context

将来的な候補:

```php
Route::controller(UserController::class, [
    Route::get('/', 'index'),
    Route::get('/{id}', 'show'),
    Route::post('/', 'create'),
]);
```

ただしHandler解決のMagicが増えるため、v1では採用しない。

---

## 30.7 Framework Adapter

将来的にはCompilerを抽象化することで他Frameworkへ対応できる可能性がある。

```text
Route Tree
├── SlimCompiler
├── MezzioCompiler
└── CustomCompiler
```

ただしv1の内部設計を過度に抽象化しない。

YAGNIを優先する。

---

# 31. 性能要件

ルート定義は通常Application起動時に1回だけdeployされるため、極端な最適化は不要。

ただし以下を満たすこと。

- 再帰処理はRoute数に対して概ねO(n)
- 不要なReflectionを使用しない
- Route登録時以外のRuntime overheadを発生させない
- HTTP Request処理中にDSL独自処理を挟まない

`deploy()` 完了後は通常のSlim Routeとして動作することを理想とする。

---

# 32. セキュリティ要件

DSLがルート定義を扱うことによって、Slim標準より危険な挙動を追加しない。

特に以下を禁止する。

- Handler文字列を `eval()` する
- Middleware文字列から独自コード生成を行う
- Pathを正規表現へ危険な形で変換する
- Controller名から任意ファイルをrequireする
- serialize/unserializeでRoute定義をロードする

Handler解決は原則Slimへ委ねる。

---

# 33. テスト要件

最低限以下をUnit Testする。

## HTTP Route

- GET
- POST
- PUT
- PATCH
- DELETE
- OPTIONS
- MAP
- ANY

## Group

- Single Group
- Nested Group
- `/` の正規化
- Root Group

## Middleware

- Single Middleware
- Multiple Middleware
- Nested Middleware
- Group + Middleware
- Middleware順序

## Tree

- Deep Nested Tree
- Sibling Routes
- Empty Group
- Invalid Child

## Deploy

- Slimへ正しいMethodで登録される
- Slimへ正しいPathで登録される
- Handlerが保持される
- Middlewareが適用される

---

# 34. Coding Policy

ライブラリコードは以下を推奨する。

- `declare(strict_types=1);`
- PSR-4
- PSR-12
- PHPStan
- PHPUnit または Pest
- Composer
- GitHub Actions

例:

```php
<?php

declare(strict_types=1);

namespace Tanahiro2010\RouterDsl;
```

---

# 35. READMEで最初に見せるコード

READMEでは概念説明より先に以下のようなコードを提示する。

```php
$routes = new Routes([
    Route::group('/api', [
        Route::middleware(AuthMiddleware::class, [
            Route::get('/users', [UserController::class, 'index']),
            Route::get('/users/{id}', [UserController::class, 'show']),
            Route::post('/users', [UserController::class, 'create']),
        ]),
    ]),
]);

$routes->deploy($app);
```

このコードを見ただけで以下が理解できることを目標とする。

- `/api` 配下
- Auth Middleware適用
- users endpoint群
- deployでSlimへ登録

---

# 36. v1 MVP Scope

v1.0.0に必須とする機能:

- `Routes`
- `Routes::deploy()`
- `Route`
- `Route::get()`
- `Route::post()`
- `Route::put()`
- `Route::patch()`
- `Route::delete()`
- `Route::options()`
- `Route::map()`
- `Route::any()`
- `Route::group()`
- `Route::middleware()`
- Nested Group
- Nested Middleware
- Multiple Middleware
- Slim 4 compiler
- Path normalization
- Middleware order preservation
- Basic validation
- Exceptions
- Unit Tests
- README
- Composer package

---

# 37. v1 Optional Scope

時間に余裕がある場合のみ実装する。

- Route name
- Route-level Fluent Middleware
- Route dump
- `toArray()`
- HEAD helper

---

# 38. v1 Out of Scope

- OpenAPI
- Resource routes
- Controller context
- Attribute routing
- Auto discovery
- Framework adapters
- File based routing

---

# 39. Success Criteria

v1成功の条件:

1. Slim標準APIよりルート構造を視覚的に把握しやすい
2. GroupとMiddlewareが同一のTreeとして自然に記述できる
3. DSL定義時にSlimへの副作用が発生しない
4. `deploy()` だけでSlimへ正常登録できる
5. Handler / MiddlewareのSlim互換性を大きく損なわない
6. 複雑なネストでも挙動を予測できる
7. DSL自体に大きなRuntime overheadがない
8. READMEのサンプルだけで基本利用方法を理解できる

---

# 40. 最終APIイメージ

v1における理想形:

```php
<?php

declare(strict_types=1);

use App\Controller\HealthController;
use App\Controller\UserController;
use App\Middleware\AuthMiddleware;
use App\Middleware\JsonMiddleware;
use Slim\Factory\AppFactory;
use Tanahiro2010\RouterDsl\Route;
use Tanahiro2010\RouterDsl\Routes;

$app = AppFactory::create();

$routes = new Routes([
    Route::get('/', HomeController::class),

    Route::group('/api', [
        Route::middleware(JsonMiddleware::class, [
            Route::get('/health', HealthController::class),

            Route::middleware(AuthMiddleware::class, [
                Route::group('/users', [
                    Route::get('/', [UserController::class, 'index']),
                    Route::get('/{id}', [UserController::class, 'show']),
                    Route::post('/', [UserController::class, 'create']),
                    Route::put('/{id}', [UserController::class, 'update']),
                    Route::delete('/{id}', [UserController::class, 'delete']),
                ]),
            ]),
        ]),
    ]),
]);

$routes->deploy($app);

$app->run();
```

---

# 41. 一文での定義

> Slim Router DSLは、Slim Frameworkのルーティングを「登録処理」ではなく「宣言的なルートツリー」として定義し、`Routes::deploy()` によってSlimへ展開する軽量DSLライブラリである。
