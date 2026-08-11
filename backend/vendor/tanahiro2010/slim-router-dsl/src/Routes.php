<?php

declare(strict_types=1);

namespace Tanahiro2010\SlimRouterDsl;

use Slim\App;
use Tanahiro2010\SlimRouterDsl\Compiler\CompiledRoute;
use Tanahiro2010\SlimRouterDsl\Compiler\RouteDumper;
use Tanahiro2010\SlimRouterDsl\Compiler\RouteFlattener;
use Tanahiro2010\SlimRouterDsl\Compiler\RouteInspector;
use Tanahiro2010\SlimRouterDsl\Compiler\RouteValidator;
use Tanahiro2010\SlimRouterDsl\Compiler\SlimRouteCompiler;
use Tanahiro2010\SlimRouterDsl\Contracts\RouteNode;
use Tanahiro2010\SlimRouterDsl\Exception\InvalidRouteNodeException;

final class Routes
{
    /**
     * @param RouteNode[] $routes
     */
    public function __construct(
        private readonly array $routes,
    ) {
        foreach ($this->routes as $route) {
            if (!$route instanceof RouteNode) {
                throw new InvalidRouteNodeException(
                    'Routes constructor requires an array of RouteNode instances.'
                );
            }
        }
    }

    public function deploy(App $app): void
    {
        $compiler = new SlimRouteCompiler($app);

        $compiler->compile($this->routes);
    }

    /**
     * @return CompiledRoute[]
     */
    public function compile(): array
    {
        return (new RouteFlattener())->flatten($this->routes);
    }

    public function findByName(string $name): ?CompiledRoute
    {
        return (new RouteInspector($this->compile()))->findByName($name);
    }

    /**
     * @return CompiledRoute[]
     */
    public function filterByMethod(string $method): array
    {
        return (new RouteInspector($this->compile()))->filterByMethod($method);
    }

    /**
     * @return CompiledRoute[]
     */
    public function findByPath(string $path): array
    {
        return (new RouteInspector($this->compile()))->findByPath($path);
    }

    /**
     * @return CompiledRoute[]
     */
    public function filterByMiddleware(string|object $middleware): array
    {
        return (new RouteInspector($this->compile()))->filterByMiddleware($middleware);
    }

    /**
     * @throws \Tanahiro2010\SlimRouterDsl\Exception\DuplicateRouteException
     * @throws \Tanahiro2010\SlimRouterDsl\Exception\DuplicateRouteNameException
     */
    public function validate(): void
    {
        (new RouteValidator())->validate($this->compile());
    }

    /**
     * @param callable(CompiledRoute): bool $predicate
     * @return CompiledRoute[]
     */
    public function filter(callable $predicate): array
    {
        return array_values(array_filter($this->compile(), $predicate));
    }

    /**
     * @return array<int, array{
     *     methods: string[],
     *     path: string,
     *     handler: mixed,
     *     middleware: array,
     *     name: string|null,
     *     metadata: array,
     * }>
     */
    public function toArray(): array
    {
        return (new RouteDumper())->toArray($this->compile());
    }

    public function dump(bool $showMiddleware = true, bool $showName = true, bool $showHandler = false): string
    {
        return (new RouteDumper())->dump($this->compile(), $showMiddleware, $showName, $showHandler);
    }
}
