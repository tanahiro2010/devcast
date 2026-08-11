<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Tanahiro2010\SlimRouterDsl\Compiler\CompiledRoute;
use Tanahiro2010\SlimRouterDsl\Route;
use Tanahiro2010\SlimRouterDsl\Routes;

final class RouteQueryTest extends TestCase
{
    private function buildRoutes(): Routes
    {
        return new Routes([
            Route::get('/', 'Home'),
            Route::group('/api', [
                Route::middleware('Auth', [
                    Route::get('/users', 'Index')->name('users.index'),
                    Route::get('/users/{id}', 'Show')->name('users.show'),
                    Route::post('/users', 'Create'),
                ]),
            ]),
        ]);
    }

    public function testCompileReturnsCompiledRouteList(): void
    {
        $routes = $this->buildRoutes();

        $compiled = $routes->compile();

        self::assertCount(4, $compiled);
        self::assertContainsOnlyInstancesOf(CompiledRoute::class, $compiled);
    }

    public function testFindByNameReturnsMatch(): void
    {
        $routes = $this->buildRoutes();

        $route = $routes->findByName('users.show');

        self::assertNotNull($route);
        self::assertSame('/api/users/{id}', $route->path);
    }

    public function testFindByNameReturnsNullWhenMissing(): void
    {
        $routes = $this->buildRoutes();

        self::assertNull($routes->findByName('does.not.exist'));
    }

    public function testFilterByMethodReturnsOnlyMatchingRoutes(): void
    {
        $routes = $this->buildRoutes();

        $postRoutes = $routes->filterByMethod('POST');

        self::assertCount(1, $postRoutes);
        self::assertSame('/api/users', $postRoutes[0]->path);
    }

    public function testFindByPathReturnsAllRoutesForThatPath(): void
    {
        $routes = new Routes([
            Route::get('/users', 'Index'),
            Route::post('/users', 'Create'),
            Route::get('/posts', 'Other'),
        ]);

        $matches = $routes->findByPath('/users');

        self::assertCount(2, $matches);
    }

    public function testFilterByMiddlewareMatchesStringMiddleware(): void
    {
        $routes = $this->buildRoutes();

        $matches = $routes->filterByMiddleware('Auth');

        self::assertCount(3, $matches);
    }

    public function testFilterByMiddlewareMatchesByObjectIdentity(): void
    {
        $middleware = new class {
        };

        $routes = new Routes([
            Route::middleware($middleware, [
                Route::get('/a', 'A'),
            ]),
            Route::get('/b', 'B'),
        ]);

        $matches = $routes->filterByMiddleware($middleware);

        self::assertCount(1, $matches);
        self::assertSame('/a', $matches[0]->path);
    }

    public function testFilterByMiddlewareMatchesObjectByClassString(): void
    {
        $middleware = new class {
        };

        $routes = new Routes([
            Route::middleware($middleware, [
                Route::get('/a', 'A'),
            ]),
        ]);

        $matches = $routes->filterByMiddleware($middleware::class);

        self::assertCount(1, $matches);
    }
}
