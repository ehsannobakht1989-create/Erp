<?php

declare(strict_types=1);

// Laravel's console exception handler can exit 0 after rendering an uncaught
// exception, so a platform script passes only with exit 0 AND a final "passed." line.
$scripts = glob(__DIR__.'/platform/*.php') ?: [];
sort($scripts, SORT_STRING);
if ($scripts === []) {
    fwrite(STDERR, "No platform tests found.\n");
    exit(1);
}
$failed = 0;
foreach ($scripts as $script) {
    $process = proc_open([PHP_BINARY, $script], [1 => ['pipe', 'w'], 2 => ['redirect', 1]], $pipes, dirname(__DIR__));
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
    }
}
echo count($scripts).' platform scripts, '.$failed." failed.\n";
exit($failed === 0 ? 0 : 1);
