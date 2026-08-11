<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Tanahiro2010\SlimRouterDsl\Compiler\RouteContext;
use Tanahiro2010\SlimRouterDsl\Nodes\RouteGroup;
use Tanahiro2010\SlimRouterDsl\Route;

final class RouteGroupTest extends TestCase
{
    public function testSingleGroup(): void
    {
        $group = Route::group('/api', [
            Route::get('/users', 'Handler'),
        ]);

        self::assertInstanceOf(RouteGroup::class, $group);
        self::assertSame('/api', $group->prefix);
        self::assertCount(1, $group->children);
    }

    public function testNestedGroup(): void
    {
        $group = Route::group('/api', [
            Route::group('/v1', [
                Route::get('/users', 'Handler'),
            ]),
        ]);

        self::assertSame('/api', $group->prefix);

        $nested = $group->children[0];
        self::assertInstanceOf(RouteGroup::class, $nested);
        self::assertSame('/v1', $nested->prefix);
    }

    public function testRootGroup(): void
    {
        $group = Route::group('', [
            Route::get('/', 'Handler'),
        ]);

        self::assertSame('', $group->prefix);
    }

    #[DataProvider('pathNormalizationProvider')]
    public function testPathNormalization(string $prefix, string $path, string $expected): void
    {
        self::assertSame($expected, RouteContext::joinPaths($prefix, $path));
    }

    /**
     * @return array<string, array{0: string, 1: string, 2: string}>
     */
    public static function pathNormalizationProvider(): array
    {
        return [
            'no trailing slash, leading slash' => ['/api', '/users', '/api/users'],
            'trailing slash, leading slash' => ['/api/', '/users', '/api/users'],
            'no trailing slash, no leading slash' => ['/api', 'users', '/api/users'],
            'root prefix' => ['', '/users', '/users'],
            'group root path' => ['/api', '/', '/api'],
            'nested groups' => [
                RouteContext::joinPaths('/api', '/v1'),
                '/users',
                '/api/v1/users',
            ],
        ];
    }
}
