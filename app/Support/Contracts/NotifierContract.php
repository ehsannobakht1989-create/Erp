<?php

declare(strict_types=1);

namespace App\Support\Contracts;

interface NotifierContract
{
    /** @param array<string, mixed> $data */
    public function notify(int $companyId, int $userId, string $channel, string $template, array $data = []): void;
}
