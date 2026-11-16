<?php

declare(strict_types=1);

namespace App\Support\Tenancy;

use Illuminate\Http\Request;

final class DenyCompanyContextResolver implements AuthorizedCompanyContextResolver
{
    public function resolve(Request $request): ?CompanySelection
    {
        return null;
    }
}
