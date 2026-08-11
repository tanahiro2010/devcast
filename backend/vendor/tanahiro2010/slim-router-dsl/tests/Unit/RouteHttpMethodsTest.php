<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Tanahiro2010\SlimRouterDsl\Exception\InvalidRouteException;
use Tanahiro2010\SlimRouterDsl\Nodes\HttpRoute;
use Tanahiro2010\SlimRouterDsl\Route;

final class RouteHttpMethodsTest extends TestCase
{
    public function testGet(): void
    {
        $route = Route::get('/users', 'Handler');

        self::assertInstanceOf(HttpRoute::class, $route);
        self::assertSame(['GET'], $route->methods);
        self::assertSame('/users', $route->path);
        self::assertSame('Handler', $route->handler);
    }

    public function testPost(): void
    {
        $route = Route::post('/users', 'Handler');

        self::assertSame(['POST'], $route->methods);
    }

    public function testPut(): void
    {
        $route = Route::put('/users/{id}', 'Handler');

        self::assertSame(['PUT'], $route->methods);
        self::assertSame('/users/{id}', $route->path);
    }

    public function testPatch(): void
    {
        $route = Route::patch('/users/{id}', 'Handler');

        self::assertSame(['PATCH'], $route->methods);
    }

    public function testDelete(): void
    {
        $route = Route::delete('/users/{id}', 'Handler');

        self::assertSame(['DELETE'], $route->methods);
    }

    public function testOptions(): void
    {
        $route = Route::options('/users', 'Handler');

        self::assertSame(['OPTIONS'], $route->methods);
    }

    public function testMap(): void
    {
        $route = Route::map(['GET', 'HEAD'], '/resource', 'Handler');

        self::assertSame(['GET', 'HEAD'], $route->methods);
        self::assertSame('/resource', $route->path);
    }

    public function testMapRejectsEmptyMethods(): void
    {
        $this->expectException(InvalidRouteException::class);

        Route::map([], '/resource', 'Handler');
    }

    public function testAny(): void
    {
        $route = Route::any('/health', 'Handler');

        self::assertSame(['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'], $route->methods);
        self::assertSame('/health', $route->path);
    }

    public function testHead(): void
    {
        $route = Route::head('/users', 'Handler');

        self::assertSame(['HEAD'], $route->methods);
        self::assertSame('/users', $route->path);
    }
}
