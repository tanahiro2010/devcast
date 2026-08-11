<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Tanahiro2010\SlimRouterDsl\Exception\InvalidRouteNodeException;
use Tanahiro2010\SlimRouterDsl\Nodes\RouteGroup;
use Tanahiro2010\SlimRouterDsl\Route;
use Tanahiro2010\SlimRouterDsl\Routes;

final class RouteTreeTest extends TestCase
{
    public function testDeepNestedTree(): void
    {
        $routes = new Routes([
            Route::group('/api', [
                Route::middleware('Json', [
                    Route::middleware('Auth', [
                        Route::group('/users', [
                            Route::get('/', 'Index'),
                            Route::get('/{id}', 'Show'),
                        ]),
                    ]),
                ]),
            ]),
        ]);

        self::assertInstanceOf(Routes::class, $routes);
    }

    public function testSiblingRoutes(): void
    {
        $group = Route::group('/api', [
            Route::get('/users', 'Index'),
            Route::post('/users', 'Create'),
        ]);

        self::assertCount(2, $group->children);
    }

    public function testEmptyGroup(): void
    {
        $group = Route::group('/api', []);

        self::assertInstanceOf(RouteGroup::class, $group);
        self::assertSame([], $group->children);
    }

    public function testInvalidChildInGroupThrows(): void
    {
        $this->expectException(InvalidRouteNodeException::class);

        Route::group('/api', ['not-a-route-node']);
    }

    public function testInvalidChildInMiddlewareThrows(): void
    {
        $this->expectException(InvalidRouteNodeException::class);

        Route::middleware('Auth', ['not-a-route-node']);
    }

    public function testInvalidTopLevelNodeInRoutesThrows(): void
    {
        $this->expectException(InvalidRouteNodeException::class);

        new Routes(['hello']);
    }
}
