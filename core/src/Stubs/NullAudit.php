<?php
declare(strict_types=1);

namespace Core\Stubs;

use Core\Contracts\AuditContract;

final class NullAudit implements AuditContract
{
    public function record(
        int $companyId,
        ?int $actorId,
        string $module,
        string $table,
        string $action,
        int|string $recordId,
        ?array $before = null,
        ?array $after = null,
    ): void {
        // No-op stub
    }
}
