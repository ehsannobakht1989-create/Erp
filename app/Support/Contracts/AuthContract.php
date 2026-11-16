<?php

declare(strict_types=1);

namespace App\Support\Contracts;

use Illuminate\Http\Request;

interface AuthContract
{
    /** @return array{id: int, name: string, email?: string, company_id?: int}|null */
    public function authenticate(Request $request): ?array;

    /** @return array{id: int, name: string, email?: string, company_id?: int}|null */
    public function user(): ?array;
}
