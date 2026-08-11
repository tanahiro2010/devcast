<?php

declare(strict_types=1);

namespace Tanahiro2010\SlimRouterDsl\Nodes;

use Tanahiro2010\SlimRouterDsl\Contracts\RouteNode;
use Tanahiro2010\SlimRouterDsl\Exception\InvalidRouteNodeException;

final readonly class RouteGroup implements RouteNode
{
    /**
     * @param RouteNode[] $children
     */
    public function __construct(
        public string $prefix,
        public array $children,
    ) {
        foreach ($this->children as $child) {
            if (!$child instanceof RouteNode) {
                throw new InvalidRouteNodeException(
                    'RouteGroup children must all be instances of RouteNode.'
                );
            }
        }
    }
}
