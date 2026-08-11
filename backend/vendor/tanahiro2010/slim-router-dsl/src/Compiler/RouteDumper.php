<?php

declare(strict_types=1);

namespace Tanahiro2010\SlimRouterDsl\Compiler;

use Closure;

/**
 * Formats an already-flattened CompiledRoute[] list as plain arrays or a
 * human-readable table. Shared by Routes::toArray()/dump() and the CLI, so
 * both operate on identical formatting logic over arbitrary (e.g. filtered)
 * CompiledRoute[] lists.
 */
final class RouteDumper
{
    /**
     * @param CompiledRoute[] $routes
     * @return array<int, array{
     *     methods: string[],
     *     path: string,
     *     handler: mixed,
     *     middleware: array,
     *     name: string|null,
     *     metadata: array,
     * }>
     */
    public function toArray(array $routes): array
    {
        return array_map(
            static fn (CompiledRoute $route): array => [
                'methods' => $route->methods,
                'path' => $route->path,
                'handler' => $route->handler,
                'middleware' => $route->middleware,
                'name' => $route->name,
                'metadata' => $route->metadata,
            ],
            $routes,
        );
    }

    /**
     * @param CompiledRoute[] $routes
     */
    public function dump(
        array $routes,
        bool $showMiddleware = true,
        bool $showName = true,
        bool $showHandler = false,
    ): string {
        $headers = ['METHOD', 'PATH'];
        if ($showName) {
            $headers[] = 'NAME';
        }
        if ($showMiddleware) {
            $headers[] = 'MIDDLEWARE';
        }
        if ($showHandler) {
            $headers[] = 'HANDLER';
        }

        $rows = array_map(
            function (CompiledRoute $route) use ($showName, $showMiddleware, $showHandler): array {
                $row = [implode(',', $route->methods), $route->path];

                if ($showName) {
                    $row[] = $route->name ?? '-';
                }
                if ($showMiddleware) {
                    $row[] = $this->formatMiddlewareList($route->middleware);
                }
                if ($showHandler) {
                    $row[] = $this->formatHandler($route->handler);
                }

                return $row;
            },
            $routes,
        );

        return self::formatTable($headers, $rows);
    }

    /**
     * @param array $middleware
     */
    private function formatMiddlewareList(array $middleware): string
    {
        if ($middleware === []) {
            return '-';
        }

        return implode(', ', array_map(
            static fn (mixed $item): string => is_object($item) ? $item::class : (string) $item,
            $middleware,
        ));
    }

    private function formatHandler(mixed $handler): string
    {
        if (is_array($handler) && count($handler) === 2) {
            [$class, $method] = $handler;
            $className = is_object($class) ? $class::class : (string) $class;

            return sprintf('%s::%s', $className, (string) $method);
        }

        if (is_string($handler)) {
            return $handler;
        }

        if ($handler instanceof Closure) {
            return 'Closure';
        }

        if (is_object($handler)) {
            return $handler::class;
        }

        return (string) $handler;
    }

    /**
     * @param string[] $headers
     * @param array<int, string[]> $rows
     */
    private static function formatTable(array $headers, array $rows): string
    {
        $widths = array_map('strlen', $headers);

        foreach ($rows as $row) {
            foreach ($row as $index => $cell) {
                $widths[$index] = max($widths[$index], strlen($cell));
            }
        }

        $formatRow = static function (array $cells) use ($widths): string {
            $padded = [];

            foreach ($cells as $index => $cell) {
                $padded[] = str_pad($cell, $widths[$index]);
            }

            return rtrim(implode('  ', $padded));
        };

        $lines = [$formatRow($headers)];

        foreach ($rows as $row) {
            $lines[] = $formatRow($row);
        }

        return implode("\n", $lines);
    }
}
