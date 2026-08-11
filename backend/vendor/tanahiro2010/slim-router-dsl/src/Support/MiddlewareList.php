<?php

declare(strict_types=1);

namespace Tanahiro2010\SlimRouterDsl\Support;

use Tanahiro2010\SlimRouterDsl\Exception\InvalidMiddlewareException;

final class MiddlewareList
{
    /**
     * @return array
     */
    public static function normalize(string|object|array $middleware): array
    {
        $middlewareList = is_array($middleware) ? $middleware : [$middleware];

        if ($middlewareList === []) {
            throw new InvalidMiddlewareException('At least one middleware is required.');
        }

        return $middlewareList;
    }
}
