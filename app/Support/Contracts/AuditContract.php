<?php

declare(strict_types=1);

namespace App\Support\Contracts;

interface AuditContract
{
    /**
     * @param  array<string, mixed>|null  $before
     * @param  array<string, mixed>|null  $after
     */
    public function record(
        int $companyId,
        ?int $actorId,
        string $module,
        string $table,
        string $action,
        int|string $recordId,
        ?array $before = null,
        ?array $after = null,
    ): void;
}
