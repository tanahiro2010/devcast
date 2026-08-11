<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Tanahiro2010\SlimRouterDsl\Exception\InvalidRouteNodeException;
use Tanahiro2010\SlimRouterDsl\Route;
use Tanahiro2010\SlimRouterDsl\Routes;

final class ValidationTest extends TestCase
{
    public function testRoutesRejectsNonRouteNodeChildren(): void
    {
        $this->expectException(InvalidRouteNodeException::class);

        new Routes(['hello']);
    }

    public function testMapDoesNotWhitelistMethodNames(): void
    {
        // v1 intentionally does not enforce an HTTP method whitelist,
        // since Slim itself may accept arbitrary methods.
        $route = Route::map(['HELLO'], '/', 'Handler');

        self::assertSame(['HELLO'], $route->methods);
    }

    public function testValidRouteTreeDoesNotThrow(): void
    {
        $routes = new Routes([
            Route::get('/', 'Home'),
            Route::group('/api', [
                Route::get('/users', 'Index'),
            ]),
        ]);

        self::assertInstanceOf(Routes::class, $routes);
    }
}
