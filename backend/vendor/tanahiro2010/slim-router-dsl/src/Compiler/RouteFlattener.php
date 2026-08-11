<?php

declare(strict_types=1);

namespace Tanahiro2010\SlimRouterDsl\Compiler;

use Tanahiro2010\SlimRouterDsl\Contracts\RouteNode;
use Tanahiro2010\SlimRouterDsl\Exception\InvalidRouteNodeException;
use Tanahiro2010\SlimRouterDsl\Nodes\ControllerGroup;
use Tanahiro2010\SlimRouterDsl\Nodes\HttpRoute;
use Tanahiro2010\SlimRouterDsl\Nodes\MiddlewareGroup;
use Tanahiro2010\SlimRouterDsl\Nodes\RouteGroup;

/**
 * Resolves a route tree into a flat list of CompiledRoute, without touching Slim.
 * Shared by SlimRouteCompiler (deploy) and Routes::dump()/toArray() (inspection).
 */
final class RouteFlattener
{
    /**
     * @param RouteNode[] $routes
     * @return CompiledRoute[]
     */
    public function flatten(array $routes): array
    {
        $compiled = [];

        $this->walk($routes, new RouteContext(), $compiled);

        return $compiled;
    }

    /**
     * @param RouteNode[] $nodes
     * @param CompiledRoute[] $compiled
     */
    private function walk(array $nodes, RouteContext $context, array &$compiled): void
    {
        foreach ($nodes as $node) {
            match (true) {
                $node instanceof HttpRoute => $compiled[] = $this->compileHttpRoute($node, $context),
                $node instanceof RouteGroup => $this->walk(
                    $node->children,
                    $context->withPrefix($node->prefix),
                    $compiled,
                ),
                $node instanceof MiddlewareGroup => $this->walk(
                    $node->children,
                    $context->withMiddleware($node->middleware),
                    $compiled,
                ),
                $node instanceof ControllerGroup => $this->walk(
                    $node->children,
                    $context->withController($node->controller),
                    $compiled,
                ),
                default => throw new InvalidRouteNodeException(
                    sprintf('Unknown RouteNode implementation: %s', $node::class)
                ),
            };
        }
    }

    private function compileHttpRoute(HttpRoute $node, RouteContext $context): CompiledRoute
    {
        return new CompiledRoute(
            $node->methods,
            RouteContext::joinPaths($context->prefix, $node->path),
            $this->resolveHandler($node->handler, $context),
            [...$context->middleware, ...$node->middleware],
            $node->name,
            $node->metadata,
        );
    }

    /**
     * Inside a Route::controller() block, a plain string handler is interpreted
     * as "call this method on the ambient controller". Outside such a block
     * (ambient controller is null), string handlers are left untouched, matching
     * v1.0.0 behavior exactly (e.g. Slim container-name resolution).
     */
    private function resolveHandler(mixed $handler, RouteContext $context): mixed
    {
        if ($context->controller !== null && is_string($handler)) {
            return [$context->controller, $handler];
        }

        return $handler;
    }
}
