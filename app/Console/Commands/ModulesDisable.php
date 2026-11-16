<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Support\Modules\ModuleState;
use Illuminate\Console\Command;

final class ModulesDisable extends Command
{
    protected $signature = 'erp:modules:disable {module}';

    protected $description = 'Disable a module on the next application boot (dependent modules must be disabled first)';

    public function handle(ModuleState $state): int
    {
        $state->set($this->argument('module'), false);
        $this->info('Module disabled. Restart long-lived workers.');

        return self::SUCCESS;
    }
}
