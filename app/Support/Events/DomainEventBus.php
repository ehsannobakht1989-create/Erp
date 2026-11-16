<?php

declare(strict_types=1);

namespace App\Support\Events;

use App\Support\Tenancy\CompanyContext;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Connection;
use LogicException;

final class DomainEventBus
{
    public function __construct(
        private readonly Connection $connection,
        private readonly Dispatcher $dispatcher,
        private readonly CompanyContext $context,
    ) {}

    /** Run only after the outermost database transaction commits; rollback drops it. */
    public function publish(DomainEvent $event): void
    {
        if ($event->companyId !== $this->context->companyId()) {
            throw new LogicException('Cross-company event is forbidden.');
        }
        $this->connection->afterCommit(fn () => $this->dispatcher->dispatch($event));
    }
}
