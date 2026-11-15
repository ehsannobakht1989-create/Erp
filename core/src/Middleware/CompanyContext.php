<?php
declare(strict_types=1);

namespace Core\Middleware;

use Core\Contracts\CompanyContract;
use Core\Http\Request;
use Core\Http\Response;

final class CompanyContext
{
    public function __construct(private readonly CompanyContract $company)
    {
    }

    public function handle(Request $request, callable $next): Response
    {
        $user = $request->attributes['user'] ?? null;
        $companyId = $this->company->resolveCompanyId($request, is_array($user) ? $user : null);
        if ($companyId !== null) {
            $request->attributes['company_id'] = $companyId;
        }
        return $next($request);
    }
}
