<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Tanahiro2010\SlimRouterDsl\Route;
use Tanahiro2010\SlimRouterDsl\Routes;

final class RouteMetadataTest extends TestCase
{
    public function testDefaultMetadataIsEmpty(): void
    {
        $route = Route::get('/users', 'Handler');

        self::assertSame([], $route->metadata);
    }

    public function testMetaSetsMetadata(): void
    {
        $route = Route::get('/users/{id}', 'Handler')->meta(['auth' => true, 'permission' => 'users.read']);

        self::assertSame(['auth' => true, 'permission' => 'users.read'], $route->metadata);
    }

    public function testMetaReturnsNewInstance(): void
    {
        $original = Route::get('/users', 'Handler');
        $withMeta = $original->meta(['auth' => true]);

        self::assertSame([], $original->metadata);
        self::assertNotSame($original, $withMeta);
    }

    public function testRepeatedMetaCallsMergeWithLaterKeysOverriding(): void
    {
        $route = Route::get('/users', 'Handler')
            ->meta(['auth' => true, 'summary' => 'List users'])
            ->meta(['auth' => false]);

        self::assertSame(['auth' => false, 'summary' => 'List users'], $route->metadata);
    }

    public function testMetaCanBeCombinedWithMiddlewareAndName(): void
    {
        $route = Route::get('/users/{id}', 'Handler')
            ->middleware('Auth')
            ->name('users.show')
            ->meta(['permission' => 'users.read']);

        self::assertSame(['Auth'], $route->middleware);
        self::assertSame('users.show', $route->name);
        self::assertSame(['permission' => 'users.read'], $route->metadata);
    }

    public function testMetadataFlowsThroughToArray(): void
    {
        $routes = new Routes([
            Route::get('/users/{id}', 'Show')->meta(['auth' => true]),
        ]);

        self::assertSame(['auth' => true], $routes->toArray()[0]['metadata']);
    }

    public function testMetadataFlowsThroughCompile(): void
    {
        $routes = new Routes([
            Route::get('/users/{id}', 'Show')->meta(['auth' => true]),
        ]);

        self::assertSame(['auth' => true], $routes->compile()[0]->metadata);
    }

    public function testFilterFindsRoutesByMetadata(): void
    {
        $routes = new Routes([
            Route::get('/users', 'Index')->meta(['auth' => true]),
            Route::get('/health', 'Health'),
        ]);

        $authRequired = $routes->filter(
            fn ($route) => $route->metadata['auth'] ?? false,
        );

        self::assertCount(1, $authRequired);
        self::assertSame('/users', $authRequired[0]->path);
    }
}
