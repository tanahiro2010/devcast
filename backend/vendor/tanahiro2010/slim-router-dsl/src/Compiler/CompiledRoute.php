<?php

declare(strict_types=1);

namespace Tanahiro2010\SlimRouterDsl\Compiler;

/**
 * A single flattened, fully-resolved route: prefix and middleware inheritance
 * have already been applied. Framework-agnostic, produced by RouteFlattener.
 */
final readonly class CompiledRoute
{
    /**
     * @param string[] $methods
     * @param array $middleware Declared (outer -> inner) order.
     * @param array $metadata Arbitrary user-defined metadata set via HttpRoute::meta().
     */
    public function __construct(
        public array $methods,
        public string $path,
        public mixed $handler,
        public array $middleware,
        public ?string $name,
        public array $metadata = [],
    ) {
    }
}
