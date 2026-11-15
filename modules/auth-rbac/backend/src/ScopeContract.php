<?php
declare(strict_types=1);

namespace Modules\AuthRbac;

interface ScopeContract
{
    /**
     * Returns data scope for a user in a company: 'own' | 'department' | 'branch' | 'all'
     */
    public function scopeFor(int $userId, int $companyId, string $module): string;
}
