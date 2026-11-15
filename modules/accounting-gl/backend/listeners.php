<?php
declare(strict_types=1);

return [
    'accounting.journal.requested' => fn(array $payload): bool => !empty($payload),
];
