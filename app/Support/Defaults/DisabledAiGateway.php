<?php

declare(strict_types=1);

namespace App\Support\Defaults;

use App\Support\Contracts\AiGatewayContract;

final class DisabledAiGateway implements AiGatewayContract
{
    public function invoke(int $companyId, int $userId, string $moduleId, string $capabilityId, array $input): array
    {
        return ['ok' => false, 'status' => 'disabled', 'output' => null, 'error' => 'AI disabled'];
    }
}
