<?php

declare(strict_types=1);

namespace App\Support\Tenancy;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class CompanyContextMiddleware
{
    public function __construct(
        private readonly CompanyContext $context,
        private readonly AuthorizedCompanyContextResolver $resolver,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $this->context->clear();
        try {
            if ($request->user() === null) {
                abort(401, 'احراز هویت لازم است.');
            }
            $selection = $this->resolver->resolve($request);
            if ($selection === null) {
                abort(403, 'دسترسی به شرکت مجاز نیست.');
            }
            $this->context->activate($selection->companyId, $selection->branchId, $selection->fiscalYearId);

            return $next($request);
        } finally {
            $this->context->clear();
        }
    }
}
