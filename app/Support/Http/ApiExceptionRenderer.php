<?php

declare(strict_types=1);

namespace App\Support\Http;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Encryption\MissingAppKeyException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

final class ApiExceptionRenderer
{
    private const STATUS = [
        400 => ['bad_request', 'درخواست نامعتبر است.'],
        401 => ['unauthenticated', 'احراز هویت لازم است.'],
        403 => ['forbidden', 'شما مجوز انجام این عملیات را ندارید.'],
        404 => ['not_found', 'مورد درخواستی یافت نشد.'],
        405 => ['method_not_allowed', 'این روش درخواست پشتیبانی نمی‌شود.'],
        409 => ['conflict', 'درخواست با وضعیت فعلی داده‌ها تعارض دارد.'],
        419 => ['session_expired', 'نشست منقضی شده است؛ دوباره تلاش کنید.'],
        422 => ['validation_failed', 'اطلاعات واردشده معتبر نیست.'],
        429 => ['too_many_requests', 'تعداد درخواست‌ها بیش از حد مجاز است؛ کمی بعد تلاش کنید.'],
        500 => ['server_error', 'خطای داخلی سرور رخ داد.'],
        503 => ['unavailable', 'سرویس موقتاً در دسترس نیست.'],
    ];

    public static function render(Throwable $e, Request $request): JsonResponse|Response|null
    {
        // Before the installer writes APP_KEY, sessions and encrypted cookies cannot work; answer without them.
        if ($e instanceof MissingAppKeyException) {
            $message = 'سامانه هنوز نصب نشده است؛ نشانی /install را باز کنید.';

            return $request->is('api/*') || $request->expectsJson()
                ? new JsonResponse(['ok' => false, 'data' => null, 'error' => ['code' => 'not_installed', 'message' => $message, 'details' => []], 'meta' => []], 503)
                : new Response('<!doctype html><html lang="fa" dir="rtl"><meta charset="utf-8"><title>ERP</title><p>'.$message.' <a href="/install">نصب</a></p></html>', 503, ['Content-Type' => 'text/html; charset=UTF-8']);
        }
        if (! $request->is('api/*') && ! $request->expectsJson()) {
            return null;
        }
        $details = [];
        $status = match (true) {
            $e instanceof ValidationException => 422,
            $e instanceof AuthenticationException => 401,
            $e instanceof AuthorizationException => 403,
            $e instanceof ModelNotFoundException => 404,
            $e instanceof HttpExceptionInterface => $e->getStatusCode(),
            default => 500,
        };
        if ($e instanceof ValidationException) {
            $details = ['fields' => $e->errors()];
        }
        [$code, $message] = self::STATUS[$status] ?? ['http_error', 'درخواست قابل انجام نیست.'];
        if ($e instanceof HttpExceptionInterface && preg_match('/\p{Arabic}/u', $e->getMessage()) === 1) {
            $message = $e->getMessage();
        }
        $meta = $status >= 500 && config('app.debug') ? ['exception' => $e::class, 'debug' => $e->getMessage()] : [];
        $response = ApiResponse::error($code, $message, $status, $details, $meta);
        if ($e instanceof HttpExceptionInterface) {
            $response->headers->add($e->getHeaders());
        }

        return $response;
    }
}
