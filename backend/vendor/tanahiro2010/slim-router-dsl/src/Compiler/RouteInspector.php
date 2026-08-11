<?php

declare(strict_types=1);

namespace Tanahiro2010\SlimRouterDsl\Compiler;

/**
 * Stateless queries over an already-flattened CompiledRoute[] list.
 */
final class RouteInspector
{
    /**
     * @param CompiledRoute[] $routes
     */
    public function __construct(
        private readonly array $routes,
    ) {
    }

    public function findByName(string $name): ?CompiledRoute
    {
        foreach ($this->routes as $route) {
            if ($route->name === $name) {
                return $route;
            }
        }

        return null;
    }

    /**
     * @return CompiledRoute[]
     */
    public function filterByMethod(string $method): array
    {
        return array_values(array_filter(
            $this->routes,
            static fn (CompiledRoute $route): bool => in_array($method, $route->methods, true),
        ));
    }

    /**
     * @return CompiledRoute[]
     */
    public function findByPath(string $path): array
    {
        return array_values(array_filter(
            $this->routes,
            static fn (CompiledRoute $route): bool => $route->path === $path,
        ));
    }

    /**
     * @return CompiledRoute[]
     */
    public function filterByMiddleware(string|object $middleware): array
    {
        return array_values(array_filter(
            $this->routes,
            fn (CompiledRoute $route): bool => $this->hasMiddleware($route, $middleware),
        ));
    }

    private function hasMiddleware(CompiledRoute $route, string|object $middleware): bool
    {
        foreach ($route->middleware as $candidate) {
            if (is_object($middleware)) {
                if ($candidate === $middleware) {
                    return true;
                }

                continue;
            }

            if ($candidate === $middleware) {
                return true;
            }

            if (is_object($candidate) && $candidate::class === $middleware) {
                return true;
            }
        }

        return false;
    }
}
