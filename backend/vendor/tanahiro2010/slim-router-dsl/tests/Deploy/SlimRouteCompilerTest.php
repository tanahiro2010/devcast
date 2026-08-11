<?php

declare(strict_types=1);

namespace Tests\Deploy;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Slim\App;
use Slim\Factory\AppFactory;
use Slim\Psr7\Factory\ServerRequestFactory;
use Tanahiro2010\SlimRouterDsl\Route;
use Tanahiro2010\SlimRouterDsl\Routes;

final class SlimRouteCompilerTest extends TestCase
{
    private App $app;

    protected function setUp(): void
    {
        $this->app = AppFactory::create();
    }

    private function request(string $method, string $uri): Request
    {
        return (new ServerRequestFactory())->createServerRequest($method, $uri);
    }

    public function testRegistersCorrectMethodAndPath(): void
    {
        $routes = new Routes([
            Route::get('/users', fn (Request $request, Response $response) => $response),
        ]);

        $routes->deploy($this->app);

        $registered = array_values($this->app->getRouteCollector()->getRoutes());

        self::assertCount(1, $registered);
        self::assertSame(['GET'], $registered[0]->getMethods());
        self::assertSame('/users', $registered[0]->getPattern());
    }

    public function testGroupPrefixIsAppliedToRegisteredPath(): void
    {
        $routes = new Routes([
            Route::group('/api', [
                Route::get('/users', fn (Request $request, Response $response) => $response),
            ]),
        ]);

        $routes->deploy($this->app);

        $registered = array_values($this->app->getRouteCollector()->getRoutes());

        self::assertSame('/api/users', $registered[0]->getPattern());
    }

    public function testHandlerIsPreserved(): void
    {
        $handler = fn (Request $request, Response $response) => $response;

        $routes = new Routes([
            Route::get('/users', $handler),
        ]);

        $routes->deploy($this->app);

        $registered = array_values($this->app->getRouteCollector()->getRoutes());

        self::assertSame($handler, $registered[0]->getCallable());
    }

    public function testMiddlewareIsAppliedAndExecutesInDeclaredOrder(): void
    {
        $order = [];

        $makeMiddleware = static function (string $name) use (&$order): MiddlewareInterface {
            return new class($name, $order) implements MiddlewareInterface {
                public function __construct(
                    private string $name,
                    private array &$order,
                ) {
                }

                public function process(Request $request, Handler $handler): Response
                {
                    $this->order[] = $this->name;

                    return $handler->handle($request);
                }
            };
        };

        $routes = new Routes([
            Route::middleware($makeMiddleware('A'), [
                Route::middleware($makeMiddleware('B'), [
                    Route::get('/', function (Request $request, Response $response) use (&$order) {
                        $order[] = 'Route';

                        return $response;
                    }),
                ]),
            ]),
        ]);

        $routes->deploy($this->app);

        $response = $this->app->handle($this->request('GET', '/'));

        self::assertSame(['A', 'B', 'Route'], $order);
        self::assertSame(200, $response->getStatusCode());
    }

    public function testCandidateAMultipleMiddlewareExecutesInArrayOrder(): void
    {
        $order = [];

        $makeMiddleware = static function (string $name) use (&$order): MiddlewareInterface {
            return new class($name, $order) implements MiddlewareInterface {
                public function __construct(
                    private string $name,
                    private array &$order,
                ) {
                }

                public function process(Request $request, Handler $handler): Response
                {
                    $this->order[] = $this->name;

                    return $handler->handle($request);
                }
            };
        };

        $routes = new Routes([
            Route::middleware([$makeMiddleware('A'), $makeMiddleware('B')], [
                Route::get('/', function (Request $request, Response $response) use (&$order) {
                    $order[] = 'Route';

                    return $response;
                }),
            ]),
        ]);

        $routes->deploy($this->app);

        $this->app->handle($this->request('GET', '/'));

        self::assertSame(['A', 'B', 'Route'], $order);
    }

    public function testRouteNameIsAppliedToSlimRoute(): void
    {
        $routes = new Routes([
            Route::get('/users/{id}', fn (Request $request, Response $response) => $response)
                ->name('users.show'),
        ]);

        $routes->deploy($this->app);

        $registered = array_values($this->app->getRouteCollector()->getRoutes());

        self::assertSame('users.show', $registered[0]->getName());
    }

    public function testRouteLevelMiddlewareExecutesClosestToHandler(): void
    {
        $order = [];

        $makeMiddleware = static function (string $name) use (&$order): MiddlewareInterface {
            return new class($name, $order) implements MiddlewareInterface {
                public function __construct(
                    private string $name,
                    private array &$order,
                ) {
                }

                public function process(Request $request, Handler $handler): Response
                {
                    $this->order[] = $this->name;

                    return $handler->handle($request);
                }
            };
        };

        $routes = new Routes([
            Route::middleware($makeMiddleware('Json'), [
                Route::get('/', function (Request $request, Response $response) use (&$order) {
                    $order[] = 'Route';

                    return $response;
                })->middleware($makeMiddleware('Auth')),
            ]),
        ]);

        $routes->deploy($this->app);

        $this->app->handle($this->request('GET', '/'));

        self::assertSame(['Json', 'Auth', 'Route'], $order);
    }
}
