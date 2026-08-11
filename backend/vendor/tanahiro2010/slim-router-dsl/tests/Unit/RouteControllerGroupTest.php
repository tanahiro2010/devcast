<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Tanahiro2010\SlimRouterDsl\Exception\InvalidRouteNodeException;
use Tanahiro2010\SlimRouterDsl\Nodes\ControllerGroup;
use Tanahiro2010\SlimRouterDsl\Route;
use Tanahiro2010\SlimRouterDsl\Routes;

final class RouteControllerGroupTest extends TestCase
{
    public function testControllerCreatesControllerGroup(): void
    {
        $group = Route::controller('UserController', [
            Route::get('/', 'index'),
        ]);

        self::assertInstanceOf(ControllerGroup::class, $group);
        self::assertSame('UserController', $group->controller);
    }

    public function testStringHandlerIsResolvedAgainstAmbientController(): void
    {
        $routes = new Routes([
            Route::controller('UserController', [
                Route::get('/users', 'index'),
                Route::get('/users/{id}', 'show'),
            ]),
        ]);

        $compiled = $routes->compile();

        self::assertSame(['UserController', 'index'], $compiled[0]->handler);
        self::assertSame(['UserController', 'show'], $compiled[1]->handler);
    }

    public function testNonStringHandlersAreLeftUntouchedInsideControllerGroup(): void
    {
        $closure = fn () => null;

        $routes = new Routes([
            Route::controller('UserController', [
                Route::get('/callback', $closure),
                Route::get('/explicit', ['OtherController', 'method']),
            ]),
        ]);

        $compiled = $routes->compile();

        self::assertSame($closure, $compiled[0]->handler);
        self::assertSame(['OtherController', 'method'], $compiled[1]->handler);
    }

    public function testStringHandlerOutsideControllerGroupIsUnaffected(): void
    {
        $routes = new Routes([
            Route::get('/health', 'HealthCheckContainerEntry'),
        ]);

        self::assertSame('HealthCheckContainerEntry', $routes->compile()[0]->handler);
    }

    public function testNestedControllerGroupInnermostWins(): void
    {
        $routes = new Routes([
            Route::controller('OuterController', [
                Route::controller('InnerController', [
                    Route::get('/', 'index'),
                ]),
            ]),
        ]);

        self::assertSame(['InnerController', 'index'], $routes->compile()[0]->handler);
    }

    public function testControllerGroupCanBeCombinedWithPrefixAndMiddleware(): void
    {
        $routes = new Routes([
            Route::group('/api', [
                Route::middleware('Auth', [
                    Route::controller('UserController', [
                        Route::get('/users', 'index'),
                    ]),
                ]),
            ]),
        ]);

        $compiled = $routes->compile()[0];

        self::assertSame('/api/users', $compiled->path);
        self::assertSame(['Auth'], $compiled->middleware);
        self::assertSame(['UserController', 'index'], $compiled->handler);
    }

    public function testControllerGroupRejectsNonRouteNodeChildren(): void
    {
        $this->expectException(InvalidRouteNodeException::class);

        Route::controller('UserController', ['not-a-route-node']);
    }
}
