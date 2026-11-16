<?php

declare(strict_types=1);

namespace App\Support\Defaults;

use App\Support\Contracts\AuditContract;
use App\Support\Contracts\DatasetContract;
use App\Support\Contracts\NotifierContract;
use App\Support\Contracts\PrintContract;
use App\Support\Contracts\WorkflowContract;
use LogicException;

/** Missing optional engines must never pretend a side effect succeeded. */
final class UnavailablePlatform implements AuditContract, DatasetContract, NotifierContract, PrintContract, WorkflowContract
{
    public function record(int $companyId, ?int $actorId, string $module, string $table, string $action, int|string $recordId, ?array $before = null, ?array $after = null): void
    {
        throw new LogicException('Audit provider is unavailable.');
    }

    public function notify(int $companyId, int $userId, string $channel, string $template, array $data = []): void
    {
        throw new LogicException('Notification provider is unavailable.');
    }

    public function render(int $companyId, string $template, array $data): string
    {
        throw new LogicException('Print provider is unavailable.');
    }

    public function transition(int $companyId, string $workflow, int|string $entityId, string $action, array $context = []): string
    {
        throw new LogicException('Workflow provider is unavailable.');
    }

    public function query(int $companyId, int $userId, string $dataset, array $query): array
    {
        throw new LogicException('Dataset provider is unavailable.');
    }
}
