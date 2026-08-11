<?php

declare(strict_types=1);

namespace Tanahiro2010\SlimRouterDsl\Compiler;

final readonly class RouteContext
{
    /**
     * @param array $middleware
     */
    public function __construct(
        public string $prefix = '',
        public array $middleware = [],
        public mixed $controller = null,
    ) {
    }

    public function withPrefix(string $prefix): self
    {
        return new self(
            self::joinPaths($this->prefix, $prefix),
            $this->middleware,
            $this->controller,
        );
    }

    /**
     * @param array $middleware
     */
    public function withMiddleware(array $middleware): self
    {
        return new self(
            $this->prefix,
            [...$this->middleware, ...$middleware],
            $this->controller,
        );
    }

    /**
     * Replaces (not merges) the ambient controller — a route can only have one.
     */
    public function withController(mixed $controller): self
    {
        return new self(
            $this->prefix,
            $this->middleware,
            $controller,
        );
    }

    public static function joinPaths(string $left, string $right): string
    {
        $normalized = rtrim($left, '/') . '/' . ltrim($right, '/');
        $normalized = (string) preg_replace('#/+#', '/', $normalized);

        if ($normalized === '') {
            return '';
        }

        if ($normalized !== '/' && str_ends_with($normalized, '/')) {
            $normalized = rtrim($normalized, '/');
        }

        return $normalized === '' ? '/' : $normalized;
    }
}
