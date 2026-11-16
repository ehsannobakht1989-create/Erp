<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Support\Events\InboxProcessor;
use Illuminate\Console\Command;

final class EventsDrain extends Command
{
    protected $signature = 'erp:events:drain {--limit=100 : Maximum inbox entries to process}';

    protected $description = 'Process due async event inbox entries, recovering stale and retrying entries';

    public function handle(InboxProcessor $processor): int
    {
        $this->info('Processed '.$processor->drain((int) $this->option('limit')).' inbox entries.');

        return self::SUCCESS;
    }
}
