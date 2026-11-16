<?php

declare(strict_types=1);

require_once dirname(__DIR__).'/app/Support/Modules/ModuleCatalog.php';
require_once dirname(__DIR__).'/app/Support/Events/EventCatalog.php';

use App\Support\Events\EventCatalog;
use App\Support\Modules\ModuleCatalog;

$root = dirname(__DIR__);
if ($argc !== 2 || ! in_array($argv[1], ['--write', '--check'], true)) {
    fwrite(STDERR, "Usage: php tools/event-catalog.php --write|--check\n");
    exit(2);
}
try {
    $content = (new EventCatalog)->markdown(ModuleCatalog::scan($root.'/modules'));
    $path = $root.'/docs/event-catalog.md';
    if ($argv[1] === '--check') {
        if (! is_file($path) || file_get_contents($path) !== $content) {
            fwrite(STDERR, "Event catalog is stale; regenerate with --write.\n");
            exit(1);
        }
    } elseif (file_put_contents($path, $content) === false) {
        throw new RuntimeException('Cannot write event catalog');
    }
} catch (Throwable $e) {
    fwrite(STDERR, $e->getMessage()."\n");
    exit(1);
}
echo "Event catalog is current.\n";
