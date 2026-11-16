<?php

declare(strict_types=1);

namespace App\Support\Ai;

/** Read-only tool callable by a model; runs with the invoking user's permissions. */
interface AiTool
{
    /** @return array{description:string, parameters:array<string,mixed>} */
    public function describe(): array;

    /** @param array<string, mixed> $arguments */
    public function run(int $companyId, int $userId, array $arguments): mixed;
}
