<?php

declare(strict_types=1);

namespace App\Support\Events;

use App\Support\Tenancy\CompanyContext;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Connection;
use LogicException;
use Throwable;

final class DomainEventBus
{
    public function __construct(
        private readonly Connection $connection,
        private readonly Dispatcher $dispatcher,
        private readonly CompanyContext $context,
        private readonly ?EventSubscriptions $subscriptions = null,
    ) {}

    /**
     * Durable async inbox rows join the caller's transaction; synchronous listeners
     * and queue triggers run only after the outermost commit. Rollback drops all.
     */
    public function publish(DomainEvent $event): void
    {
        if ($event->companyId !== $this->context->companyId()) {
            throw new LogicException('Cross-company event is forbidden.');
        }
        $ids = [];
        foreach ($this->subscriptions?->listenersFor($event->event) ?? [] as $listener) {
            $now = now();
            $row = ['listener' => $listener, 'event_id' => $event->eventId, 'event' => $event->event, 'company_id' => $event->companyId, 'envelope' => json_encode($event->toArray(), JSON_THROW_ON_ERROR), 'status' => 'pending', 'attempts' => 0, 'available_at' => $now, 'created_at' => $now, 'updated_at' => $now];
            if ($this->connection->table('core_event_inbox')->insertOrIgnore($row) === 1) {
                $ids[] = (int) $this->connection->table('core_event_inbox')->where(['listener' => $listener, 'event_id' => $event->eventId])->value('id');
            }
        }
        $this->connection->afterCommit(function () use ($event, $ids): void {
            foreach ($ids as $id) {
                try {
                    dispatch(new ProcessEventInbox($id));
                } catch (Throwable $e) {
                    report($e);
                }
            }
            $this->dispatcher->dispatch($event);
        });
    }
}
