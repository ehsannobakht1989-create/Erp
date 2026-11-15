<?php
declare(strict_types=1);

return [
    'inventory.stock.first_posted' => fn(array $payload): bool => !empty($payload),
];
