<?php

declare(strict_types=1);

namespace App\Support\Events;

/** Async handlers run inside a transaction with the event's company context; they must be idempotent per business key. */
interface DomainEventHandler
{
    public function handle(DomainEvent $event): void;
}
