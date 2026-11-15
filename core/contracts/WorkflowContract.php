<?php
declare(strict_types=1);

namespace Core\Contracts;

interface WorkflowContract
{
    /** @param array<string, mixed> $context */
    public function transition(int $companyId, string $workflow, int|string $entityId, string $action, array $context = []): string;
}
