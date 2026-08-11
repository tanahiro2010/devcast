<?php

declare(strict_types=1);

namespace Tanahiro2010\SlimRouterDsl\Compiler;

use Tanahiro2010\SlimRouterDsl\Exception\DuplicateRouteException;
use Tanahiro2010\SlimRouterDsl\Exception\DuplicateRouteNameException;

/**
 * Detects duplicate method+path and duplicate route name definitions
 * in an already-flattened CompiledRoute[] list.
 */
final class RouteValidator
{
    /**
     * @param CompiledRoute[] $routes
     */
    public function validate(array $routes): void
    {
        $this->assertNoDuplicateRoutes($routes);
        $this->assertNoDuplicateNames($routes);
    }

    /**
     * @param CompiledRoute[] $routes
     */
    private function assertNoDuplicateRoutes(array $routes): void
    {
        $seen = [];

        foreach ($routes as $route) {
            foreach ($route->methods as $method) {
                $key = sprintf('%s %s', strtoupper($method), $route->path);

                if (isset($seen[$key])) {
                    throw new DuplicateRouteException(
                        sprintf('%s is defined multiple times.', $key)
                    );
                }

                $seen[$key] = true;
            }
        }
    }

    /**
     * @param CompiledRoute[] $routes
     */
    private function assertNoDuplicateNames(array $routes): void
    {
        $seen = [];

        foreach ($routes as $route) {
            if ($route->name === null) {
                continue;
            }

            if (isset($seen[$route->name])) {
                throw new DuplicateRouteNameException(
                    sprintf('Route name "%s" is already defined.', $route->name)
                );
            }

            $seen[$route->name] = true;
        }
    }
}
