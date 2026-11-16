<?php

declare(strict_types=1);

namespace App\Models;

use Laravel\Sanctum\PersonalAccessToken as SanctumToken;

final class PersonalAccessToken extends SanctumToken
{
    protected $table = 'core_personal_access_tokens';
}
