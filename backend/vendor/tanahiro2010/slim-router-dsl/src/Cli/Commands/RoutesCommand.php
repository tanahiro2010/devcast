<?php

declare(strict_types=1);

namespace Tanahiro2010\SlimRouterDsl\Cli\Commands;

use Tanahiro2010\SlimRouterDsl\Compiler\CompiledRoute;
use Tanahiro2010\SlimRouterDsl\Compiler\RouteDumper;
use Tanahiro2010\SlimRouterDsl\Routes;

/**
 * Formats a Routes tree as either a dump()-style table or JSON, optionally
 * filtered by --method= or --name=.
 */
final class RoutesCommand
{
    /**
     * @param array{json?: bool, method?: string, name?: string} $options
     */
    public function execute(Routes $routes, array $options): string
    {
        $compiled = $this->resolveCompiledRoutes($routes, $options);
        $dumper = new RouteDumper();

        if ($options['json'] ?? false) {
            return json_encode(
                $dumper->toArray($compiled),
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES,
            ) . "\n";
        }

        return $dumper->dump($compiled) . "\n";
    }

    /**
     * @param array{json?: bool, method?: string, name?: string} $options
     * @return CompiledRoute[]
     */
    private function resolveCompiledRoutes(Routes $routes, array $options): array
    {
        if (isset($options['name'])) {
            $match = $routes->findByName($options['name']);

            return $match !== null ? [$match] : [];
        }

        if (isset($options['method'])) {
            return $routes->filterByMethod(strtoupper($options['method']));
        }

        return $routes->compile();
    }
}
