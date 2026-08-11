<?php

declare(strict_types=1);

namespace Tests\Cli;

use PHPUnit\Framework\TestCase;
use Tanahiro2010\SlimRouterDsl\Cli\Commands\ValidateCommand;
use Tanahiro2010\SlimRouterDsl\Route;
use Tanahiro2010\SlimRouterDsl\Routes;

final class ValidateCommandTest extends TestCase
{
    public function testReturnsZeroAndPrintsSuccessForValidRoutes(): void
    {
        $routes = new Routes([
            Route::get('/users/{id}', 'Show')->name('users.show'),
        ]);

        ob_start();
        $exitCode = (new ValidateCommand())->execute($routes);
        $output = ob_get_clean();

        self::assertSame(0, $exitCode);
        self::assertStringContainsString('No validation errors.', $output);
    }

    public function testReturnsOneForDuplicateRoutes(): void
    {
        $routes = new Routes([
            Route::get('/users/{id}', 'ShowA'),
            Route::get('/users/{id}', 'ShowB'),
        ]);

        ob_start();
        $exitCode = (new ValidateCommand())->execute($routes);
        ob_get_clean();

        self::assertSame(1, $exitCode);
    }

    public function testReturnsOneForDuplicateRouteNames(): void
    {
        $routes = new Routes([
            Route::get('/users/{id}', 'Show')->name('users.show'),
            Route::get('/members/{id}', 'Show')->name('users.show'),
        ]);

        ob_start();
        $exitCode = (new ValidateCommand())->execute($routes);
        ob_get_clean();

        self::assertSame(1, $exitCode);
    }
}
