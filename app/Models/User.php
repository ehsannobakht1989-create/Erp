<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

/** Global identity; company membership and roles belong to platform modules. */
final class User extends Authenticatable
{
    use HasApiTokens;

    protected $table = 'core_users';

    protected $fillable = ['name', 'email', 'password', 'is_active', 'locale'];

    protected $hidden = ['password', 'remember_token'];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return ['password' => 'hashed', 'is_active' => 'boolean', 'last_login_at' => 'datetime'];
    }

    public function setEmailAttribute(string $value): void
    {
        $this->attributes['email'] = mb_strtolower(trim($value));
    }
}
