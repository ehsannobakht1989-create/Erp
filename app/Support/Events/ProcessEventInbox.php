<?php

declare(strict_types=1);

namespace App\Support\Events;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

/** Low-latency trigger; the inbox row, not the queue job, is the durable record. */
final class ProcessEventInbox implements ShouldQueue
{
    use Queueable;

    public int $tries = 1;

    public function __construct(public readonly int $inboxId) {}

    public function handle(InboxProcessor $processor): void
    {
        $processor->process($this->inboxId);
    }
}
