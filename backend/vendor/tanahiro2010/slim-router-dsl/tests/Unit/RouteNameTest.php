<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Tanahiro2010\SlimRouterDsl\Route;

final class RouteNameTest extends TestCase
{
    public function testDefaultNameIsNull(): void
    {
        $route = Route::get('/users/{id}', 'Handler');

        self::assertNull($route->name);
    }

    public function testFluentNameSetsName(): void
    {
        $route = Route::get('/users/{id}', 'Handler')->name('users.show');

        self::assertSame('users.show', $route->name);
    }

    public function testFluentNameReturnsNewInstance(): void
    {
        $original = Route::get('/users/{id}', 'Handler');
        $named = $original->name('users.show');

        self::assertNull($original->name);
        self::assertNotSame($original, $named);
    }

    public function testNameAndMiddlewareCanBeCombined(): void
    {
        $route = Route::get('/users/{id}', 'Handler')
            ->middleware('AuthMiddleware')
            ->name('users.show');

        self::assertSame('users.show', $route->name);
        self::assertSame(['AuthMiddleware'], $route->middleware);
    }
}
