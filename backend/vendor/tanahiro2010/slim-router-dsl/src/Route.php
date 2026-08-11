<?php

declare(strict_types=1);

namespace Tanahiro2010\SlimRouterDsl;

use Tanahiro2010\SlimRouterDsl\Contracts\RouteNode;
use Tanahiro2010\SlimRouterDsl\Exception\InvalidRouteException;
use Tanahiro2010\SlimRouterDsl\Nodes\ControllerGroup;
use Tanahiro2010\SlimRouterDsl\Nodes\HttpRoute;
use Tanahiro2010\SlimRouterDsl\Nodes\MiddlewareGroup;
use Tanahiro2010\SlimRouterDsl\Nodes\RouteGroup;
use Tanahiro2010\SlimRouterDsl\Support\MiddlewareList;

final class Route
{
    /**
     * @var string[]
     */
    private const ANY_METHODS = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'];

    /**
     * Action key => [HTTP method, path suffix appended to the resource prefix].
     *
     * @var array<string, array{0: string, 1: string}>
     */
    private const RESOURCE_ACTIONS = [
        'index' => ['GET', '/'],
        'show' => ['GET', '/{id}'],
        'create' => ['POST', '/'],
        'update' => ['PUT', '/{id}'],
        'patch' => ['PATCH', '/{id}'],
        'delete' => ['DELETE', '/{id}'],
    ];

    public static function get(string $path, mixed $handler): HttpRoute
    {
        return self::map(['GET'], $path, $handler);
    }

    public static function post(string $path, mixed $handler): HttpRoute
    {
        return self::map(['POST'], $path, $handler);
    }

    public static function put(string $path, mixed $handler): HttpRoute
    {
        return self::map(['PUT'], $path, $handler);
    }

    public static function patch(string $path, mixed $handler): HttpRoute
    {
        return self::map(['PATCH'], $path, $handler);
    }

    public static function delete(string $path, mixed $handler): HttpRoute
    {
        return self::map(['DELETE'], $path, $handler);
    }

    public static function options(string $path, mixed $handler): HttpRoute
    {
        return self::map(['OPTIONS'], $path, $handler);
    }

    public static function head(string $path, mixed $handler): HttpRoute
    {
        return self::map(['HEAD'], $path, $handler);
    }

    public static function any(string $path, mixed $handler): HttpRoute
    {
        return self::map(self::ANY_METHODS, $path, $handler);
    }

    /**
     * @param string[] $methods
     */
    public static function map(array $methods, string $path, mixed $handler): HttpRoute
    {
        return new HttpRoute($methods, $path, $handler);
    }

    /**
     * @param RouteNode[] $children
     */
    public static function group(string $prefix, array $children): RouteGroup
    {
        return new RouteGroup($prefix, $children);
    }

    /**
     * @param string|object|array $middleware A single middleware, or an array of middleware.
     * @param RouteNode[] $children
     */
    public static function middleware(string|object|array $middleware, array $children): MiddlewareGroup
    {
        return new MiddlewareGroup(MiddlewareList::normalize($middleware), $children);
    }

    /**
     * @param RouteNode[] $children
     */
    public static function controller(mixed $controller, array $children): ControllerGroup
    {
        return new ControllerGroup($controller, $children);
    }

    /**
     * Generates the standard `index/show/create/update/patch/delete` CRUD routes
     * under $prefix, e.g. Route::resource('/users', UserController::class) yields:
     *
     *   GET     /users
     *   GET     /users/{id}
     *   POST    /users
     *   PUT     /users/{id}
     *   PATCH   /users/{id}
     *   DELETE  /users/{id}
     *
     * Each route's handler is [$controller, $actionKey] (e.g. [UserController::class, 'index']).
     *
     * @param string[]|null $only Restrict generation to these action keys.
     * @param string[]|null $except Exclude these action keys. Mutually exclusive with $only.
     */
    public static function resource(
        string $prefix,
        mixed $controller,
        ?array $only = null,
        ?array $except = null,
    ): RouteGroup {
        if ($only !== null && $except !== null) {
            throw new InvalidRouteException('Route::resource() cannot use $only and $except together.');
        }

        $actions = array_keys(self::RESOURCE_ACTIONS);

        if ($only !== null) {
            $actions = array_values(array_intersect($actions, $only));
        } elseif ($except !== null) {
            $actions = array_values(array_diff($actions, $except));
        }

        $routes = array_map(
            static function (string $action) use ($controller): HttpRoute {
                [$method, $pathSuffix] = self::RESOURCE_ACTIONS[$action];

                return self::map([$method], $pathSuffix, [$controller, $action]);
            },
            $actions,
        );

        return self::group($prefix, $routes);
    }
}
