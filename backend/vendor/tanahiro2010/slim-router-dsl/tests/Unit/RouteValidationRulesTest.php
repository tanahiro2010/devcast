<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Slim\Factory\AppFactory;
use Tanahiro2010\SlimRouterDsl\Exception\DuplicateRouteException;
use Tanahiro2010\SlimRouterDsl\Exception\DuplicateRouteNameException;
use Tanahiro2010\SlimRouterDsl\Route;
use Tanahiro2010\SlimRouterDsl\Routes;

final class RouteValidationRulesTest extends TestCase
{
    public function testValidateDoesNotThrowForAValidTree(): void
    {
        $routes = new Routes([
            Route::get('/users/{id}', 'Show')->name('users.show'),
            Route::post('/users', 'Create')->name('users.create'),
        ]);

        $routes->validate();

        $this->expectNotToPerformAssertions();
    }

    public function testValidateThrowsOnDuplicateMethodAndPath(): void
    {
        $routes = new Routes([
            Route::get('/users/{id}', 'ShowA'),
            Route::get('/users/{id}', 'ShowB'),
        ]);

        $this->expectException(DuplicateRouteException::class);
        $this->expectExceptionMessage('GET /users/{id} is defined multiple times.');

        $routes->validate();
    }

    public function testValidateAllowsSamePathWithDifferentMethods(): void
    {
        $routes = new Routes([
            Route::get('/users/{id}', 'Show'),
            Route::post('/users/{id}', 'Create'),
        ]);

        $routes->validate();

        $this->expectNotToPerformAssertions();
    }

    public function testValidateThrowsOnDuplicateRouteName(): void
    {
        $routes = new Routes([
            Route::get('/users/{id}', 'Show')->name('users.show'),
            Route::get('/members/{id}', 'Show')->name('users.show'),
        ]);

        $this->expectException(DuplicateRouteNameException::class);
        $this->expectExceptionMessage('Route name "users.show" is already defined.');

        $routes->validate();
    }

    public function testValidateChecksDuplicateRoutesBeforeDuplicateNames(): void
    {
        $routes = new Routes([
            Route::get('/users/{id}', 'ShowA')->name('a'),
            Route::get('/users/{id}', 'ShowB')->name('b'),
        ]);

        $this->expectException(DuplicateRouteException::class);

        $routes->validate();
    }

    public function testDeployDoesNotAutomaticallyValidate(): void
    {
        $routes = new Routes([
            Route::get('/users/{id}', 'ShowA'),
            Route::get('/users/{id}', 'ShowB'),
        ]);

        // Duplicate routes exist, but deploy() itself must not throw —
        // validation is opt-in, not baked into deploy().
        $routes->deploy(AppFactory::create());

        $this->expectNotToPerformAssertions();
    }
}
