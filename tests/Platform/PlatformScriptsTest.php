<?php

declare(strict_types=1);

namespace Tests\Platform;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class PlatformScriptsTest extends TestCase
{
    /** @return array<string, array{string}> */
    public static function scripts(): array
    {
        $scripts = [];
        foreach (glob(dirname(__DIR__).'/platform/*.php') ?: [] as $script) {
            $scripts[basename($script)] = [$script];
        }

        return $scripts;
    }

    #[DataProvider('scripts')]
    public function test_platform_script_completes(string $script): void
    {
        // @phpstan-ignore argument.type (PHP 7.4+ documents the ['redirect', 1] descriptor)
        $process = proc_open([PHP_BINARY, $script], [1 => ['pipe', 'w'], 2 => ['redirect', 1]], $pipes, dirname(__DIR__, 2));
        self::assertIsResource($process);
        $output = (string) stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        self::assertSame(0, proc_close($process), $output);
        $lines = preg_split('/\R/', trim($output)) ?: [];
        self::assertMatchesRegularExpression('/ passed\.$/D', (string) end($lines), $output);
    }
}
