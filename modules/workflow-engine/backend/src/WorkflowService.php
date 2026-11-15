<?php
declare(strict_types=1);

namespace Modules\WorkflowEngine;

use Core\Contracts\WorkflowContract;
use Core\Database\Connection;

final class WorkflowService implements WorkflowContract
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function start(int $companyId, string $workflow, int|string $entityId, string $initialState = 'draft'): void
    {
        $now = gmdate('Y-m-d H:i:s');
        $exists = $this->db->fetchOne(
            'SELECT id FROM workflow_engine__instances WHERE company_id = ? AND workflow_code = ? AND entity_id = ?',
            [$companyId, $workflow, (string) $entityId]
        );
        if ($exists === null) {
            $this->db->execute(
                'INSERT INTO workflow_engine__instances (company_id, workflow_code, entity_id, current_state, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?)',
                [$companyId, $workflow, (string) $entityId, $initialState, $now, $now]
            );
        }
    }

    public function transition(int $companyId, string $workflow, int|string $entityId, string $action, array $context = []): string
    {
        $now = gmdate('Y-m-d H:i:s');
        $inst = $this->db->fetchOne(
            'SELECT * FROM workflow_engine__instances WHERE company_id = ? AND workflow_code = ? AND entity_id = ?',
            [$companyId, $workflow, (string) $entityId]
        );
        if ($inst === null) {
            $this->start($companyId, $workflow, $entityId, 'draft');
            $inst = $this->db->fetchOne(
                'SELECT * FROM workflow_engine__instances WHERE company_id = ? AND workflow_code = ? AND entity_id = ?',
                [$companyId, $workflow, (string) $entityId]
            );
        }

        $fromState = (string) ($inst['current_state'] ?? 'draft');
        $toState = (string) ($context['to_state'] ?? $action);

        $this->db->execute(
            'UPDATE workflow_engine__instances SET current_state = ?, updated_at = ? WHERE id = ?',
            [$toState, $now, $inst['id']]
        );
        $this->db->execute(
            'INSERT INTO workflow_engine__history (company_id, instance_id, from_state, to_state, action, actor_id, comment, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
            [$companyId, $inst['id'], $fromState, $toState, $action, $context['actor_id'] ?? null, $context['comment'] ?? null, $now]
        );

        return $toState;
    }

    /** @return array<int, string> */
    public function supportedEvents(): array
    {
        return ['workflow-engine.instance.started', 'workflow-engine.instance.transitioned'];
    }
}
