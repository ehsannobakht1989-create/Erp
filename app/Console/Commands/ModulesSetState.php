<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Support\Modules\ModuleState;
use Illuminate\Console\Command;

final class ModulesSetState extends Command
{
    protected $signature = 'erp:modules:enable {module} {--disable : Disable instead of enable}';

    protected $description = 'Persist deployment-wide activation for the next application boot';

    public function handle(ModuleState $state): int
    {
        $state->set($this->argument('module'), ! $this->option('disable'));
        $this->info('Module state saved. Restart long-lived workers; changes apply on the next application boot.');

        return self::SUCCESS;
    }
}
