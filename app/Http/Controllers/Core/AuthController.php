<?php

declare(strict_types=1);

namespace App\Http\Controllers\Core;

use App\Models\PersonalAccessToken;
use App\Models\User;
use App\Support\Http\ApiResponse;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use LogicException;

/** Public authentication endpoints; business routes still require can: abilities. */
final class AuthController
{
    public function login(Request $request): JsonResponse
    {
        /** @var array{email:string,password:string,device_name?:string|null} $data */
        $data = $request->validate([
            'email' => ['required', 'string', 'email', 'max:190'],
            'password' => ['required', 'string', 'max:1024'],
            'device_name' => ['nullable', 'string', 'max:100'],
        ]);
        $user = User::query()->where('email', mb_strtolower(trim($data['email'])))->first();
        $valid = $user !== null && Hash::check($data['password'], $user->getAuthPassword());
        if ($user === null) {
            Hash::make($data['password']);
        }
        if (! $valid || ! $user->getAttribute('is_active')) {
            throw ValidationException::withMessages(['email' => ['ایمیل یا رمز عبور نادرست است یا حساب غیرفعال است.']]);
        }
        $user->forceFill(['last_login_at' => now()])->save();
        if (($data['device_name'] ?? null) !== null) {
            return ApiResponse::ok(['user' => $this->present($user), 'token' => $user->createToken($data['device_name'])->plainTextToken]);
        }
        if (! $request->hasSession()) {
            throw ValidationException::withMessages(['device_name' => ['برای ورود بدون نشست مرورگر، نام دستگاه الزامی است.']]);
        }
        $this->sessionGuard()->login($user);
        $request->session()->regenerate();

        return ApiResponse::ok(['user' => $this->present($user)]);
    }

    public function me(Request $request): JsonResponse
    {
        return ApiResponse::ok(['user' => $this->present($this->user($request))]);
    }

    public function logout(Request $request): JsonResponse
    {
        $token = $this->user($request)->currentAccessToken();
        if ($token instanceof PersonalAccessToken) {
            $token->delete();
        } else {
            $this->sessionGuard()->logout();
            if ($request->hasSession()) {
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }
        }

        return ApiResponse::ok(null);
    }

    private function sessionGuard(): StatefulGuard
    {
        $guard = Auth::guard('web');
        if (! $guard instanceof StatefulGuard) {
            throw new LogicException('The web guard must be session based.');
        }

        return $guard;
    }

    private function user(Request $request): User
    {
        $user = $request->user();
        if (! $user instanceof User) {
            throw new LogicException('Authenticated core user required.');
        }

        return $user;
    }

    /** @return array{id:int,name:string,email:string,locale:string} */
    private function present(User $user): array
    {
        return ['id' => (int) $user->getKey(), 'name' => (string) $user->getAttribute('name'), 'email' => (string) $user->getAttribute('email'), 'locale' => (string) $user->getAttribute('locale')];
    }
}
