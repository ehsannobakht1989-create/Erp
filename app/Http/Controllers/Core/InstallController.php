<?php

declare(strict_types=1);

namespace App\Http\Controllers\Core;

use App\Support\Install\Installer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use RuntimeException;
use Symfony\Component\HttpFoundation\Cookie;

/**
 * Runs without sessions or response()/abort() helpers, which resolve the encrypted session before APP_KEY exists.
 * A SameSite=Strict double-submit token blocks cross-site posts.
 */
final class InstallController
{
    private const COOKIE = 'erp_install_token';

    public function __construct(private readonly Installer $installer) {}

    public function show(Request $request): Response
    {
        return $this->installer->locked() ? new Response('', 404) : $this->page($request, [], 200);
    }

    public function store(Request $request): Response
    {
        if ($this->installer->locked()) {
            return new Response('', 404);
        }
        $token = (string) $request->cookie(self::COOKIE);
        if (strlen($token) !== 64 || ! hash_equals($token, (string) $request->input('_install_token'))) {
            return $this->page($request, ['نشست نصب نامعتبر است؛ صفحه را دوباره باز کنید.'], 419);
        }
        $validator = Validator::make($request->all(), [
            'db_connection' => ['required', 'in:mysql,sqlite'],
            'db_host' => ['required_if:db_connection,mysql', 'nullable', 'string', 'max:190'],
            'db_port' => ['nullable', 'integer', 'between:1,65535'],
            'db_database' => ['required', 'string', 'max:255'],
            'db_username' => ['required_if:db_connection,mysql', 'nullable', 'string', 'max:190'],
            'db_password' => ['nullable', 'string', 'max:255'],
            'app_url' => ['required', 'url', 'max:255'],
            'admin_name' => ['required', 'string', 'max:190'],
            'admin_email' => ['required', 'email', 'max:190'],
            'admin_password' => ['required', 'string', 'min:10', 'max:255', 'confirmed'],
        ]);
        if ($validator->fails()) {
            return $this->page($request, $validator->errors()->all(), 422);
        }
        try {
            $result = $this->installer->install($validator->validated());
        } catch (RuntimeException $e) {
            return $this->page($request, [$e->getMessage()], 422);
        }

        return new Response(view('install', ['done' => true, 'seeded' => $result['seeded'], 'base' => base_path(), 'requirements' => [], 'errors' => [], 'token' => '', 'old' => []])->render(), 200, ['Content-Type' => 'text/html; charset=UTF-8']);
    }

    /** @param list<string> $errors */
    private function page(Request $request, array $errors, int $status): Response
    {
        $token = bin2hex(random_bytes(32));
        $old = $request->except(['admin_password', 'admin_password_confirmation', 'db_password', '_install_token']);

        $response = new Response(view('install', ['done' => false, 'requirements' => $this->installer->requirements(), 'errors' => $errors, 'token' => $token, 'old' => $old])->render(), $status, ['Content-Type' => 'text/html; charset=UTF-8']);
        $response->headers->setCookie(new Cookie(self::COOKIE, $token, time() + 3600, '/install', null, $request->isSecure(), true, false, Cookie::SAMESITE_STRICT));

        return $response;
    }
}
