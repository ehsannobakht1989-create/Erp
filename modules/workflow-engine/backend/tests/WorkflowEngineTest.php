<?php
declare(strict_types=1);

use Core\Database\Connection;
use Core\Database\Migrator;
use Modules\WorkflowEngine\WorkflowService;

return function (string $root): void {
    $db = Connection::fromDsn('sqlite::memory:');
    (new Migrator($db, $root))->migrate(['workflow-engine'], false);

    $wf = new WorkflowService($db);
    $wf->start(1, 'receipt_approval', 55, 'draft');
    $next = $wf->transition(1, 'receipt_approval', 55, 'submit', ['to_state' => 'pending_approval', 'actor_id' => 1]);
    assertEquals('pending_approval', $next);
};
