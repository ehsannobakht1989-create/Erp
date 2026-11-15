<?php
declare(strict_types=1);

return [
    'mrp.planned_order.firmed' => fn(array $payload): bool => !empty($payload),
    'sales.order.confirmed' => fn(array $payload): bool => !empty($payload),
    'mes.operation.reported' => fn(array $payload): bool => !empty($payload),
    'quality-inspection.lot.decided' => fn(array $payload): bool => !empty($payload),
    'eco-plm.change.released' => fn(array $payload): bool => !empty($payload),
];
