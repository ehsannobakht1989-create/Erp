<?php

declare(strict_types=1);

namespace App\Support\Contracts;

interface AiGatewayContract
{
    /**
     * @param  array<string, mixed>  $input
     * @return array{ok: bool, status: string, output: mixed, proposal_ids?: list<int>, error?: string}
     */
    public function invoke(
        int $companyId,
        int $userId,
        string $moduleId,
        string $capabilityId,
        array $input,
    ): array;
}
