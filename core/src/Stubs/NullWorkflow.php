<?php
declare(strict_types=1);

namespace Core\Stubs;

use Core\Contracts\WorkflowContract;

final class NullWorkflow implements WorkflowContract
{
    public function transition(int $companyId, string $workflow, int|string $entityId, string $action, array $context = []): string
    {
        return $action;
    }
}
