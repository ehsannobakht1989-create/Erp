<?php

declare(strict_types=1);

namespace App\Support\Contracts;

interface PrintContract
{
    /** @param array<string, mixed> $data */
    public function render(int $companyId, string $template, array $data): string;
}
