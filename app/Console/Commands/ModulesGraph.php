<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Support\Modules\ModuleCatalog;
use Illuminate\Console\Command;

final class ModulesGraph extends Command
{
    protected $signature = 'erp:modules:graph';

    protected $description = 'Print hard and optional dependencies as JSON without activating modules';

    public function handle(ModuleCatalog $catalog): int
    {
        $graph = [];
        foreach ($catalog->manifests() as $id => $manifest) {
            $graph[$id] = ['requires' => $manifest['requires'], 'optional' => $manifest['optional']];
        }
        $this->line(json_encode($graph, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));

        return self::SUCCESS;
    }
}
