<?php
declare(strict_types=1);

namespace Core\Contracts;

use Core\Http\Request;

interface CompanyContract
{
    public function resolveCompanyId(Request $request, ?array $user): ?int;
}
