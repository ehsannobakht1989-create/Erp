<?php
declare(strict_types=1);

use Core\Autoloader;

$root = dirname(__DIR__);
require_once $root . '/core/src/Autoloader.php';
(new Autoloader($root))->register();

function assertTrue(bool $cond, string $msg = 'Assertion failed'): void
{
    if (!$cond) {
        throw new RuntimeException($msg);
    }
}

function assertEquals(mixed $expected, mixed $actual, string $msg = ''): void
{
    if ($expected !== $actual) {
        $e = var_export($expected, true);
        $a = var_export($actual, true);
        throw new RuntimeException(($msg !== '' ? "$msg: " : '') . "Expected $e, got $a");
    }
}

$testFiles = array_merge(
    glob($root . '/core/tests/*Test.php') ?: [],
    glob($root . '/modules/*/backend/tests/*Test.php') ?: []
);
sort($testFiles);

$passed = 0;
$failed = 0;

foreach ($testFiles as $file) {
    $name = basename($file, '.php');
    try {
        $fn = require $file;
        if (is_callable($fn)) {
            $fn($root);
        }
        echo "PASS: $name\n";
        $passed++;
    } catch (Throwable $e) {
        echo "FAIL: $name — {$e->getMessage()} ({$e->getFile()}:{$e->getLine()})\n";
        $failed++;
    }
}

echo sprintf("\nTests: %d passed, %d failed\n", $passed, $failed);
exit($failed > 0 ? 1 : 0);
