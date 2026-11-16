<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Support\Modules\ModuleCatalog;
use Illuminate\Console\Command;

final class ModulesList extends Command
{
    protected $signature = 'erp:modules:list';

    protected $description = 'List available modules and whether they are enabled by config';

    public function handle(ModuleCatalog $catalog): int
    {
        $enabled = config('erp-modules.enabled', []);
        $catalog->ordered($enabled);
        $rows = [];
        foreach ($catalog->manifests() as $id => $manifest) {
            $rows[] = [$id, $manifest['status'] ?? 'unknown', in_array($id, $enabled, true) ? 'yes' : 'no'];
        }
        $this->table(['Module', 'Status', 'Enabled'], $rows);

        return self::SUCCESS;
    }
}
