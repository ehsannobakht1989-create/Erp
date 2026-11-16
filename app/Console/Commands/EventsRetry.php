<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Support\Events\InboxProcessor;
use Illuminate\Console\Command;

final class EventsRetry extends Command
{
    protected $signature = 'erp:events:retry {id : Dead-letter inbox entry ID}';

    protected $description = 'Return a dead-letter event inbox entry to the retry queue';

    public function handle(InboxProcessor $processor): int
    {
        if (! ctype_digit((string) $this->argument('id')) || ! $processor->retryDead((int) $this->argument('id'))) {
            $this->error('Dead-letter entry not found.');

            return self::FAILURE;
        }
        $this->info('Entry scheduled for retry.');

        return self::SUCCESS;
    }
}
