<?php

declare(strict_types=1);
use Erp\Architecture\Checker;

require __DIR__.'/architecture/Checker.php';

$root = dirname(__DIR__);
foreach (array_slice($argv, 1) as $argument) {
    if (str_starts_with($argument, '--root=')) {
        $root = rtrim(substr($argument, 7), '/');
    } else {
        fwrite(STDERR, "Usage: php tools/architecture-check.php [--root=path]\n");
        exit(2);
    }
}
$violations = (new Checker($root))->run();
foreach ($violations as $violation) {
    echo $violation, "\n";
}
echo count($violations)." architecture violation(s).\n";
exit($violations === [] ? 0 : 1);
