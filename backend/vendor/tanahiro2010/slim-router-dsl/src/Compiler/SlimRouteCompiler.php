<?php

declare(strict_types=1);

namespace Tanahiro2010\SlimRouterDsl\Compiler;

use Slim\App;
use Tanahiro2010\SlimRouterDsl\Contracts\RouteNode;

final class SlimRouteCompiler
{
    public function __construct(
        private readonly App $app,
    ) {
    }

    /**
     * @param RouteNode[] $routes
     */
    public function compile(array $routes): void
    {
        $flattener = new RouteFlattener();

        foreach ($flattener->flatten($routes) as $compiledRoute) {
            $this->registerRoute($compiledRoute);
        }
    }

    private function registerRoute(CompiledRoute $compiledRoute): void
    {
        $route = $this->app->map($compiledRoute->methods, $compiledRoute->path, $compiledRoute->handler);

        // DSL order (outer -> inner) must run in that order at request time.
        // Slim's Route::add() is LIFO, so registering in reverse DSL order
        // makes the outermost middleware execute first.
        foreach (array_reverse($compiledRoute->middleware) as $middleware) {
            $route->add($middleware);
        }

        if ($compiledRoute->name !== null) {
            $route->setName($compiledRoute->name);
        }
    }
}
