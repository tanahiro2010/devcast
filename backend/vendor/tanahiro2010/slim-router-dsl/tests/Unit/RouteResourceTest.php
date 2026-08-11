<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Tanahiro2010\SlimRouterDsl\Exception\InvalidRouteException;
use Tanahiro2010\SlimRouterDsl\Nodes\RouteGroup;
use Tanahiro2010\SlimRouterDsl\Route;
use Tanahiro2010\SlimRouterDsl\Routes;

final class RouteResourceTest extends TestCase
{
    public function testGeneratesTheStandardCrudRoutes(): void
    {
        $resource = Route::resource('/users', 'UserController');

        self::assertInstanceOf(RouteGroup::class, $resource);

        $routes = new Routes([$resource]);

        self::assertSame([
            ['GET'], ['GET'], ['POST'], ['PUT'], ['PATCH'], ['DELETE'],
        ], array_map(fn ($r) => $r->methods, $routes->compile()));

        self::assertSame([
            '/users',
            '/users/{id}',
            '/users',
            '/users/{id}',
            '/users/{id}',
            '/users/{id}',
        ], array_map(fn ($r) => $r->path, $routes->compile()));

        self::assertSame([
            ['UserController', 'index'],
            ['UserController', 'show'],
            ['UserController', 'create'],
            ['UserController', 'update'],
            ['UserController', 'patch'],
            ['UserController', 'delete'],
        ], array_map(fn ($r) => $r->handler, $routes->compile()));
    }

    public function testOnlyRestrictsGeneratedActions(): void
    {
        $routes = new Routes([
            Route::resource('/users', 'UserController', only: ['index', 'show']),
        ]);

        $compiled = $routes->compile();

        self::assertCount(2, $compiled);
        self::assertSame(['GET'], $compiled[0]->methods);
        self::assertSame('/users', $compiled[0]->path);
        self::assertSame(['GET'], $compiled[1]->methods);
        self::assertSame('/users/{id}', $compiled[1]->path);
    }

    public function testExceptExcludesGivenActions(): void
    {
        $routes = new Routes([
            Route::resource('/users', 'UserController', except: ['delete']),
        ]);

        $compiled = $routes->compile();

        self::assertCount(5, $compiled);
        self::assertNotContains('DELETE', array_merge(...array_map(fn ($r) => $r->methods, $compiled)));
    }

    public function testOnlyAndExceptTogetherThrows(): void
    {
        $this->expectException(InvalidRouteException::class);

        Route::resource('/users', 'UserController', only: ['index'], except: ['show']);
    }

    public function testResourceCanBeNestedInsideGroupAndMiddleware(): void
    {
        $routes = new Routes([
            Route::group('/api', [
                Route::middleware('Auth', [
                    Route::resource('/users', 'UserController', only: ['index']),
                ]),
            ]),
        ]);

        $compiled = $routes->compile();

        self::assertCount(1, $compiled);
        self::assertSame('/api/users', $compiled[0]->path);
        self::assertSame(['Auth'], $compiled[0]->middleware);
    }
}
