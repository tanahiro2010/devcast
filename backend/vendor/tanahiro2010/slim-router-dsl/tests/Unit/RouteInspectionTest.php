<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Tanahiro2010\SlimRouterDsl\Route;
use Tanahiro2010\SlimRouterDsl\Routes;

final class RouteInspectionTest extends TestCase
{
    public function testToArrayFlattensSimpleTree(): void
    {
        $routes = new Routes([
            Route::get('/', 'Home'),
            Route::group('/api', [
                Route::get('/users', 'Index'),
                Route::post('/users', 'Create'),
            ]),
        ]);

        self::assertSame([
            [
                'methods' => ['GET'],
                'path' => '/',
                'handler' => 'Home',
                'middleware' => [],
                'name' => null,
                'metadata' => [],
            ],
            [
                'methods' => ['GET'],
                'path' => '/api/users',
                'handler' => 'Index',
                'middleware' => [],
                'name' => null,
                'metadata' => [],
            ],
            [
                'methods' => ['POST'],
                'path' => '/api/users',
                'handler' => 'Create',
                'middleware' => [],
                'name' => null,
                'metadata' => [],
            ],
        ], $routes->toArray());
    }

    public function testToArrayIncludesInheritedAndRouteLevelMiddlewareInOrder(): void
    {
        $routes = new Routes([
            Route::middleware('Json', [
                Route::get('/users/{id}', 'Show')
                    ->middleware('Auth')
                    ->name('users.show'),
            ]),
        ]);

        $result = $routes->toArray();

        self::assertSame(['Json', 'Auth'], $result[0]['middleware']);
        self::assertSame('users.show', $result[0]['name']);
    }

    public function testDumpFormatsMethodAndPathAsATable(): void
    {
        $routes = new Routes([
            Route::get('/', 'Home'),
            Route::group('/api', [
                Route::get('/users', 'Index'),
                Route::post('/users', 'Create'),
            ]),
        ]);

        $expected = implode("\n", [
            'METHOD  PATH        NAME  MIDDLEWARE',
            'GET     /           -     -',
            'GET     /api/users  -     -',
            'POST    /api/users  -     -',
        ]);

        self::assertSame($expected, $routes->dump());
    }

    public function testDumpJoinsMultipleMethodsWithComma(): void
    {
        $routes = new Routes([
            Route::map(['GET', 'HEAD'], '/resource', 'Handler'),
        ]);

        $expected = implode("\n", [
            'METHOD    PATH       NAME  MIDDLEWARE',
            'GET,HEAD  /resource  -     -',
        ]);

        self::assertSame($expected, $routes->dump());
    }

    public function testDumpCanHideColumnsAndShowHandler(): void
    {
        $routes = new Routes([
            Route::middleware('Auth', [
                Route::get('/users/{id}', ['UserController', 'show'])->name('users.show'),
            ]),
        ]);

        $expected = implode("\n", [
            'METHOD  PATH         HANDLER',
            'GET     /users/{id}  UserController::show',
        ]);

        self::assertSame($expected, $routes->dump(showMiddleware: false, showName: false, showHandler: true));
    }
}
