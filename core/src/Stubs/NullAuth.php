<?php
declare(strict_types=1);

namespace Core\Stubs;

use Core\Contracts\AuthContract;
use Core\Http\Request;

final class NullAuth implements AuthContract
{
    /** @var array{id: int, name: string, email?: string, company_id?: int}|null */
    private ?array $currentUser = ['id' => 1, 'name' => 'Dev User', 'company_id' => 1];

    public function authenticate(Request $request): ?array
    {
        return $this->currentUser;
    }

    public function user(): ?array
    {
        return $this->currentUser;
    }
}
