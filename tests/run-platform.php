<?php

declare(strict_types=1);

// Laravel's console exception handler can exit 0 after rendering an uncaught
// exception, so a platform script passes only with exit 0 AND a final "passed." line.
// --env=phpunit applies the <env> values from phpunit.xml (as PHPUnit does) to every script.
$environment = null;
foreach (array_slice($argv, 1) as $argument) {
    if ($argument === '--env=phpunit') {
        $xml = simplexml_load_file(dirname(__DIR__).'/phpunit.xml');
        if ($xml === false) {
            fwrite(STDERR, "Cannot read phpunit.xml\n");
            exit(2);
        }
        $environment = getenv();
        foreach ($xml->xpath('//php/env') ?: [] as $env) {
            $environment[(string) $env['name']] = (string) $env['value'];
        }
    } else {
        fwrite(STDERR, "Usage: php tests/run-platform.php [--env=phpunit]\n");
        exit(2);
    }
}
$scripts = glob(__DIR__.'/platform/*.php') ?: [];
sort($scripts, SORT_STRING);
if ($scripts === []) {
    fwrite(STDERR, "No platform tests found.\n");
    exit(1);
}
$failed = 0;
foreach ($scripts as $script) {
    // @phpstan-ignore argument.type (PHP 7.4+ documents the ['redirect', 1] descriptor)
    $process = proc_open([PHP_BINARY, $script], [1 => ['pipe', 'w'], 2 => ['redirect', 1]], $pipes, dirname(__DIR__), $environment);
    if (! is_resource($process)) {
        fwrite(STDERR, 'Cannot start '.$script."\n");
        exit(1);
    }
    $output = (string) stream_get_contents($pipes[1]);
    fclose($pipes[1]);
    $code = proc_close($process);
    $lines = preg_split('/\R/', trim($output)) ?: [];
    $passed = $code === 0 && preg_match('/ passed\.$/D', (string) end($lines)) === 1;
    echo ($passed ? 'PASS ' : 'FAIL ').basename($script)."\n";
    if (! $passed) {
        $failed++;
        echo $output, "\n(exit code $code)\n";
        if (getenv('GITHUB_ACTIONS') === 'true') {
            // Annotations remain readable through the checks API when raw logs are not.
            $tail = implode("\n", array_slice(array_values(array_filter(array_map('trim', $lines), fn (string $l) => $l !== '')), -12));
            echo '::error file=tests/platform/'.basename($script).',title=Platform script failed::'.strtr($tail."\n(exit code $code)", ['%' => '%25', "\r" => '%0D', "\n" => '%0A'])."\n";
        }
    }
}
echo count($scripts).' platform scripts'.($environment === null ? '' : ' (phpunit env)').', '.$failed." failed.\n";
exit($failed === 0 ? 0 : 1);
