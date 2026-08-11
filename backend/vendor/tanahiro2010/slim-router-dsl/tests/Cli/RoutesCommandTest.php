<?php

declare(strict_types=1);

namespace Tests\Cli;

use PHPUnit\Framework\TestCase;
use Tanahiro2010\SlimRouterDsl\Cli\Commands\RoutesCommand;
use Tanahiro2010\SlimRouterDsl\Route;
use Tanahiro2010\SlimRouterDsl\Routes;

final class RoutesCommandTest extends TestCase
{
    private function buildRoutes(): Routes
    {
        return new Routes([
            Route::get('/', 'Home')->name('home'),
            Route::group('/api', [
                Route::get('/users', 'Index')->name('users.index'),
                Route::post('/users', 'Create'),
            ]),
        ]);
    }

    public function testDefaultOutputIsDumpTable(): void
    {
        $output = (new RoutesCommand())->execute($this->buildRoutes(), []);

        self::assertStringContainsString('METHOD', $output);
        self::assertStringContainsString('/api/users', $output);
    }

    public function testJsonOptionOutputsAllRoutesAsJson(): void
    {
        $output = (new RoutesCommand())->execute($this->buildRoutes(), ['json' => true]);

        $decoded = json_decode($output, true);

        self::assertIsArray($decoded);
        self::assertCount(3, $decoded);
        self::assertSame('/api/users', $decoded[1]['path']);
    }

    public function testMethodOptionFiltersRoutes(): void
    {
        $output = (new RoutesCommand())->execute($this->buildRoutes(), ['method' => 'post']);

        self::assertStringContainsString('POST', $output);
        self::assertStringNotContainsString('GET', $output);
    }

    public function testNameOptionFiltersToSingleRoute(): void
    {
        $output = (new RoutesCommand())->execute(
            $this->buildRoutes(),
            ['name' => 'users.index', 'json' => true],
        );

        $decoded = json_decode($output, true);

        self::assertCount(1, $decoded);
        self::assertSame('users.index', $decoded[0]['name']);
    }

    public function testNameOptionReturnsEmptyListWhenNotFound(): void
    {
        $output = (new RoutesCommand())->execute(
            $this->buildRoutes(),
            ['name' => 'does.not.exist', 'json' => true],
        );

        self::assertSame([], json_decode($output, true));
    }
}
