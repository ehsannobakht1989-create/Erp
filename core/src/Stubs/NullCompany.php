<?php
declare(strict_types=1);

namespace Core\Stubs;

use Core\Contracts\CompanyContract;
use Core\Http\Request;

final class NullCompany implements CompanyContract
{
    public function resolveCompanyId(Request $request, ?array $user): ?int
    {
        $header = $request->header('X-Company-Id');
        if ($header !== null && is_numeric($header)) {
            return (int) $header;
        }
        return isset($user['company_id']) ? (int) $user['company_id'] : 1;
    }
}
