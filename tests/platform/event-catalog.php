<?php

declare(strict_types=1);

require dirname(__DIR__, 2).'/vendor/autoload.php';

use App\Support\Events\EventCatalog;
use App\Support\Modules\ModuleCatalog;

$path = sys_get_temp_dir().'/erp-events-'.bin2hex(random_bytes(8));
mkdir($path);
try {
    foreach ([
        'producer' => ['emits' => ['producer.record.created'], 'listens' => []],
        'consumer' => ['emits' => [], 'listens' => ['producer.record.created', 'absent.record.posted']],
    ] as $id => $events) {
        mkdir($path.'/'.$id);
        file_put_contents($path.'/'.$id.'/module.json', json_encode(['id' => $id, 'requires' => [], 'optional' => []] + $events, JSON_THROW_ON_ERROR));
    }
    $catalog = ModuleCatalog::scan($path);
    $generator = new EventCatalog;
    $markdown = $generator->markdown($catalog);
    foreach (['| `producer.record.created` | `producer` | `consumer` |', '| `absent.record.posted` | — | `consumer` |'] as $row) {
        if (! str_contains($markdown, $row)) {
            throw new RuntimeException('Missing or incorrect event row: '.$row);
        }
    }
    if ($generator->markdown($catalog) !== $markdown || strpos($markdown, '`absent.record.posted`') > strpos($markdown, '`producer.record.created`')) {
        throw new RuntimeException('Catalog must be deterministic and sorted');
    }
    echo "Event catalog generation tests passed.\n";
} finally {
    foreach (glob($path.'/*/module.json') ?: [] as $file) {
        unlink($file);
        rmdir(dirname($file));
    }
    rmdir($path);
}
