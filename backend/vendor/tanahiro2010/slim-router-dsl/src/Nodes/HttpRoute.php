<?php

declare(strict_types=1);

namespace Tanahiro2010\SlimRouterDsl\Nodes;

use Tanahiro2010\SlimRouterDsl\Contracts\RouteNode;
use Tanahiro2010\SlimRouterDsl\Exception\InvalidRouteException;
use Tanahiro2010\SlimRouterDsl\Support\MiddlewareList;

final readonly class HttpRoute implements RouteNode
{
    /**
     * @param string[] $methods
     * @param array $middleware Route-level middleware, applied closest to the handler.
     * @param array $metadata Arbitrary user-defined metadata (authorization, docs, tags, ...).
     */
    public function __construct(
        public array $methods,
        public string $path,
        public mixed $handler,
        public array $middleware = [],
        public ?string $name = null,
        public array $metadata = [],
    ) {
        if ($this->methods === []) {
            throw new InvalidRouteException('HttpRoute requires at least one HTTP method.');
        }
    }

    public function middleware(string|object|array $middleware): self
    {
        return new self(
            $this->methods,
            $this->path,
            $this->handler,
            [...$this->middleware, ...MiddlewareList::normalize($middleware)],
            $this->name,
            $this->metadata,
        );
    }

    public function name(string $name): self
    {
        return new self($this->methods, $this->path, $this->handler, $this->middleware, $name, $this->metadata);
    }

    /**
     * @param array $metadata Merged over existing metadata; later keys override earlier ones.
     */
    public function meta(array $metadata): self
    {
        return new self(
            $this->methods,
            $this->path,
            $this->handler,
            $this->middleware,
            $this->name,
            [...$this->metadata, ...$metadata],
        );
    }
}
