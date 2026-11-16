<?php

declare(strict_types=1);

namespace App\Support\Events;

use App\Support\Tenancy\CompanyContext;
use Illuminate\Database\Connection;
use Throwable;

final class InboxProcessor
{
    public function __construct(
        private readonly Connection $db,
        private readonly EventSubscriptions $subscriptions,
        private readonly CompanyContext $context,
        private readonly int $maxAttempts = 5,
        private readonly int $baseDelaySeconds = 30,
        private readonly int $staleAfterSeconds = 900,
    ) {}

    /**
     * Processes due entries for registered listeners; entries of disabled modules stay pending.
     *
     * @phpstan-impure
     */
    public function drain(int $limit = 100): int
    {
        $listeners = $this->subscriptions->listeners();
        if ($listeners === []) {
            return 0;
        }
        $this->db->table('core_event_inbox')->where('status', 'processing')->where('locked_at', '<', now()->subSeconds($this->staleAfterSeconds))
            ->update(['status' => 'retrying', 'locked_at' => null, 'last_error' => 'Worker stopped while processing.', 'updated_at' => now()]);
        $ids = $this->db->table('core_event_inbox')->whereIn('status', ['pending', 'retrying'])->whereIn('listener', $listeners)
            ->where('available_at', '<=', now())->orderBy('id')->limit(max(1, $limit))->pluck('id');
        foreach ($ids as $id) {
            $this->process((int) $id);
        }

        return count($ids);
    }

    /** @phpstan-impure */
    public function process(int $id): string
    {
        $listener = $this->db->table('core_event_inbox')->where('id', $id)->value('listener');
        $handlerClass = is_string($listener) ? $this->subscriptions->handler($listener) : null;
        if ($handlerClass === null) {
            return 'skipped';
        }
        $claimed = $this->db->transaction(function () use ($id) {
            $row = $this->db->table('core_event_inbox')->where('id', $id)->lockForUpdate()->first();
            if ($row === null || ! in_array($row->status, ['pending', 'retrying'], true) || now()->lt($row->available_at)) {
                return null;
            }
            $this->db->table('core_event_inbox')->where('id', $id)->update(['status' => 'processing', 'attempts' => $row->attempts + 1, 'locked_at' => now(), 'updated_at' => now()]);
            $row->attempts++;

            return $row;
        });
        if ($claimed === null) {
            return 'skipped';
        }
        $previous = $this->context->snapshot();
        try {
            $event = DomainEvent::fromArray(json_decode((string) $claimed->envelope, true, 512, JSON_THROW_ON_ERROR));
            $this->context->activate($event->companyId);
            $this->db->transaction(function () use ($handlerClass, $event, $id): void {
                app($handlerClass)->handle($event);
                $this->db->table('core_event_inbox')->where('id', $id)->update(['status' => 'processed', 'processed_at' => now(), 'locked_at' => null, 'last_error' => null, 'updated_at' => now()]);
            });

            return 'processed';
        } catch (Throwable $e) {
            $status = $claimed->attempts >= $this->maxAttempts ? 'dead' : 'retrying';
            $delay = min(3600, $this->baseDelaySeconds * 2 ** max(0, $claimed->attempts - 1));
            $this->db->table('core_event_inbox')->where('id', $id)->update(['status' => $status, 'available_at' => now()->addSeconds($delay), 'locked_at' => null, 'last_error' => mb_substr($e::class.': '.$e->getMessage(), 0, 2000), 'updated_at' => now()]);
            report($e);

            return $status;
        } finally {
            $this->context->restore($previous);
        }
    }

    public function retryDead(int $id): bool
    {
        return $this->db->table('core_event_inbox')->where(['id' => $id, 'status' => 'dead'])
            ->update(['status' => 'retrying', 'attempts' => 0, 'available_at' => now(), 'updated_at' => now()]) === 1;
    }

    public function prune(int $days): int
    {
        return $this->db->table('core_event_inbox')->where('status', 'processed')->where('processed_at', '<', now()->subDays(max(1, $days)))->delete();
    }
}
