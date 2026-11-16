<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Support\Events\InboxProcessor;
use Illuminate\Console\Command;

final class EventsPrune extends Command
{
    protected $signature = 'erp:events:prune {--days= : Keep processed entries for this many days}';

    protected $description = 'Delete processed event inbox entries older than the retention period';

    public function handle(InboxProcessor $processor): int
    {
        $days = (int) ($this->option('days') ?? config('erp.events.prune_days', 30));
        $this->info('Pruned '.$processor->prune($days).' processed inbox entries.');

        return self::SUCCESS;
    }
}
