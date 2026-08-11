<?php

declare(strict_types=1);

namespace Tanahiro2010\SlimRouterDsl\Cli\Commands;

use Tanahiro2010\SlimRouterDsl\Exception\RouterDslException;
use Tanahiro2010\SlimRouterDsl\Routes;

final class ValidateCommand
{
    public function execute(Routes $routes): int
    {
        try {
            $routes->validate();
        } catch (RouterDslException $exception) {
            fwrite(STDERR, $exception->getMessage() . "\n");

            return 1;
        }

        echo "No validation errors.\n";

        return 0;
    }
}
