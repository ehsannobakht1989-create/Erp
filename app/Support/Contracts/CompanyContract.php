<?php

declare(strict_types=1);

namespace App\Support\Contracts;

use Illuminate\Http\Request;

interface CompanyContract
{
    public function resolveCompanyId(Request $request, ?array $user): ?int;
}
