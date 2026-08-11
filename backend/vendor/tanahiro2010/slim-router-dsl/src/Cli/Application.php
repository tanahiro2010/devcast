<?php

declare(strict_types=1);

namespace Tanahiro2010\SlimRouterDsl\Cli;

use RuntimeException;
use Tanahiro2010\SlimRouterDsl\Cli\Commands\RoutesCommand;
use Tanahiro2010\SlimRouterDsl\Cli\Commands\ValidateCommand;
use Tanahiro2010\SlimRouterDsl\Routes;

/**
 * Minimal hand-rolled CLI dispatcher (no symfony/console — the command
 * surface is small enough that a routing/option-parsing framework would be
 * disproportionate dependency weight for a library).
 */
final class Application
{
    private const USAGE = "Usage: router-dsl <routes|validate> [--bootstrap=path] [--json] [--method=METHOD] [--name=NAME]\n";

    /**
     * @param string[] $argv
     */
    public function run(array $argv): int
    {
        $command = $argv[1] ?? null;

        if ($command === null) {
            fwrite(STDERR, self::USAGE);

            return 1;
        }

        $options = $this->parseOptions(array_slice($argv, 2));

        try {
            $routes = $this->loadRoutes($options['bootstrap'] ?? null);
        } catch (RuntimeException $exception) {
            fwrite(STDERR, $exception->getMessage() . "\n");

            return 2;
        }

        return match ($command) {
            'routes' => $this->runRoutesCommand($routes, $options),
            'validate' => (new ValidateCommand())->execute($routes),
            default => $this->unknownCommand($command),
        };
    }

    /**
     * @param array{json?: bool, method?: string, name?: string} $options
     */
    private function runRoutesCommand(Routes $routes, array $options): int
    {
        echo (new RoutesCommand())->execute($routes, $options);

        return 0;
    }

    private function unknownCommand(string $command): int
    {
        fwrite(STDERR, sprintf('Unknown command "%s".%s', $command, "\n"));
        fwrite(STDERR, self::USAGE);

        return 1;
    }

    /**
     * @param string[] $args
     * @return array{json: bool, method?: string, name?: string, bootstrap?: string}
     */
    private function parseOptions(array $args): array
    {
        $options = ['json' => false];

        foreach ($args as $arg) {
            if ($arg === '--json') {
                $options['json'] = true;

                continue;
            }

            foreach (['method', 'name', 'bootstrap'] as $key) {
                $prefix = "--{$key}=";

                if (str_starts_with($arg, $prefix)) {
                    $options[$key] = substr($arg, strlen($prefix));

                    continue 2;
                }
            }
        }

        return $options;
    }

    private function loadRoutes(?string $bootstrapPath): Routes
    {
        $path = $bootstrapPath ?? (getcwd() . '/routes.php');

        if (!is_file($path)) {
            throw new RuntimeException(sprintf('Bootstrap file not found: %s', $path));
        }

        $routes = require $path;

        if (!$routes instanceof Routes) {
            throw new RuntimeException(
                sprintf('Bootstrap file "%s" must return a Routes instance.', $path)
            );
        }

        return $routes;
    }
}
