<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Tanahiro2010\SlimRouterDsl\Exception\InvalidMiddlewareException;
use Tanahiro2010\SlimRouterDsl\Nodes\MiddlewareGroup;
use Tanahiro2010\SlimRouterDsl\Nodes\RouteGroup;
use Tanahiro2010\SlimRouterDsl\Route;

final class RouteMiddlewareTest extends TestCase
{
    public function testSingleMiddleware(): void
    {
        $group = Route::middleware('AuthMiddleware', [
            Route::get('/me', 'Handler'),
        ]);

        self::assertInstanceOf(MiddlewareGroup::class, $group);
        self::assertSame(['AuthMiddleware'], $group->middleware);
    }

    public function testMultipleMiddlewareCandidateA(): void
    {
        $group = Route::middleware(['AuthMiddleware', 'JsonMiddleware'], [
            Route::get('/me', 'Handler'),
        ]);

        self::assertSame(['AuthMiddleware', 'JsonMiddleware'], $group->middleware);
    }

    public function testMultipleMiddlewareCandidateBIsNestedGroups(): void
    {
        $outer = Route::middleware('AuthMiddleware', [
            Route::middleware('JsonMiddleware', [
                Route::get('/me', 'Handler'),
            ]),
        ]);

        self::assertSame(['AuthMiddleware'], $outer->middleware);

        $inner = $outer->children[0];
        self::assertInstanceOf(MiddlewareGroup::class, $inner);
        self::assertSame(['JsonMiddleware'], $inner->middleware);
    }

    public function testMiddlewareRejectsEmptyArray(): void
    {
        $this->expectException(InvalidMiddlewareException::class);

        Route::middleware([], [
            Route::get('/me', 'Handler'),
        ]);
    }

    public function testGroupPlusMiddleware(): void
    {
        $group = Route::group('/api', [
            Route::middleware('AuthMiddleware', [
                Route::get('/me', 'Handler'),
            ]),
        ]);

        self::assertInstanceOf(RouteGroup::class, $group);

        $middlewareGroup = $group->children[0];
        self::assertInstanceOf(MiddlewareGroup::class, $middlewareGroup);
        self::assertSame(['AuthMiddleware'], $middlewareGroup->middleware);
    }

    public function testMiddlewareOrderIsPreservedInDeclarationOrder(): void
    {
        $group = Route::middleware(['A', 'B', 'C'], [
            Route::get('/', 'Handler'),
        ]);

        self::assertSame(['A', 'B', 'C'], $group->middleware);
    }

    public function testFluentMiddlewareOnHttpRoute(): void
    {
        $route = Route::get('/me', 'Handler')->middleware('AuthMiddleware');

        self::assertSame(['AuthMiddleware'], $route->middleware);
    }

    public function testFluentMiddlewareAcceptsArray(): void
    {
        $route = Route::get('/me', 'Handler')->middleware(['AuthMiddleware', 'JsonMiddleware']);

        self::assertSame(['AuthMiddleware', 'JsonMiddleware'], $route->middleware);
    }

    public function testFluentMiddlewareIsChainableAndAppends(): void
    {
        $route = Route::get('/me', 'Handler')
            ->middleware('AuthMiddleware')
            ->middleware('JsonMiddleware');

        self::assertSame(['AuthMiddleware', 'JsonMiddleware'], $route->middleware);
    }

    public function testFluentMiddlewareRejectsEmptyArray(): void
    {
        $this->expectException(InvalidMiddlewareException::class);

        Route::get('/me', 'Handler')->middleware([]);
    }

    public function testFluentMiddlewareReturnsNewInstance(): void
    {
        $original = Route::get('/me', 'Handler');
        $withMiddleware = $original->middleware('AuthMiddleware');

        self::assertSame([], $original->middleware);
        self::assertNotSame($original, $withMiddleware);
    }
}
