<?php

declare(strict_types=1);

namespace App\Support\Tenancy;

use Illuminate\Http\Request;

interface AuthorizedCompanyContextResolver
{
    /** Return null unless the authenticated user may access all selected context values. */
    public function resolve(Request $request): ?CompanySelection;
}
