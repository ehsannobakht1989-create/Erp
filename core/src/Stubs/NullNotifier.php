<?php
declare(strict_types=1);

namespace Core\Stubs;

use Core\Contracts\NotifierContract;

final class NullNotifier implements NotifierContract
{
    public function notify(int $companyId, int $userId, string $channel, string $template, array $data = []): void
    {
        // No-op stub
    }
}
