<?php

declare(strict_types=1);

namespace Tests\Cli;

use PHPUnit\Framework\TestCase;
use Tanahiro2010\SlimRouterDsl\Cli\Application;

final class ApplicationTest extends TestCase
{
    private const FIXTURES = __DIR__ . '/../Fixtures';

    public function testRoutesCommandPrintsDumpTableForBootstrapFile(): void
    {
        ob_start();
        $exitCode = (new Application())->run([
            'router-dsl',
            'routes',
            '--bootstrap=' . self::FIXTURES . '/routes.php',
        ]);
        $output = ob_get_clean();

        self::assertSame(0, $exitCode);
        self::assertStringContainsString('/api/users', $output);
    }

    public function testRoutesCommandWithJsonFlag(): void
    {
        ob_start();
        (new Application())->run([
            'router-dsl',
            'routes',
            '--bootstrap=' . self::FIXTURES . '/routes.php',
            '--json',
        ]);
        $output = ob_get_clean();

        $decoded = json_decode($output, true);

        self::assertCount(3, $decoded);
    }

    public function testValidateCommandReturnsZeroForValidBootstrapFile(): void
    {
        ob_start();
        $exitCode = (new Application())->run([
            'router-dsl',
            'validate',
            '--bootstrap=' . self::FIXTURES . '/routes.php',
        ]);
        ob_get_clean();

        self::assertSame(0, $exitCode);
    }

    public function testValidateCommandReturnsOneForDuplicateRoutesFixture(): void
    {
        ob_start();
        $exitCode = (new Application())->run([
            'router-dsl',
            'validate',
            '--bootstrap=' . self::FIXTURES . '/duplicate-routes.php',
        ]);
        ob_get_clean();

        self::assertSame(1, $exitCode);
    }

    public function testMissingBootstrapFileReturnsTwo(): void
    {
        $exitCode = (new Application())->run([
            'router-dsl',
            'routes',
            '--bootstrap=' . self::FIXTURES . '/does-not-exist.php',
        ]);

        self::assertSame(2, $exitCode);
    }

    public function testBootstrapFileNotReturningRoutesReturnsTwo(): void
    {
        $exitCode = (new Application())->run([
            'router-dsl',
            'routes',
            '--bootstrap=' . self::FIXTURES . '/invalid-bootstrap.php',
        ]);

        self::assertSame(2, $exitCode);
    }

    public function testMissingCommandReturnsOne(): void
    {
        $exitCode = (new Application())->run(['router-dsl']);

        self::assertSame(1, $exitCode);
    }

    public function testUnknownCommandReturnsOne(): void
    {
        $exitCode = (new Application())->run([
            'router-dsl',
            'bogus',
            '--bootstrap=' . self::FIXTURES . '/routes.php',
        ]);

        self::assertSame(1, $exitCode);
    }
}
