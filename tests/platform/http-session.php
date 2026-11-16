<?php

declare(strict_types=1);

require dirname(__DIR__, 2).'/vendor/autoload.php';

use App\Models\User;
use Illuminate\Contracts\Console\Kernel as ConsoleKernel;
use Illuminate\Contracts\Http\Kernel as HttpKernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

function sessionCheck(bool $condition, string $message): void
{
    if (! $condition) {
        throw new RuntimeException($message);
    }
}
// Laravel skips CSRF verification when APP_ENV=testing; this test must exercise the real middleware.
foreach (['CACHE_STORE' => 'array', 'APP_ENV' => 'local'] as $name => $value) {
    putenv($name.'='.$value);
    $_ENV[$name] = $_SERVER[$name] = $value;
}
$dir = sys_get_temp_dir().'/erp-session-'.bin2hex(random_bytes(6));
mkdir($dir.'/sessions', 0777, true);
touch($dir.'/erp.sqlite');
$key = 'base64:'.base64_encode(random_bytes(32));
// Every simulated request boots a fresh application, as PHP-FPM does; only the database and session files persist.
$boot = function () use ($dir, $key) {
    $app = require dirname(__DIR__, 2).'/bootstrap/app.php';
    $app->make(ConsoleKernel::class)->bootstrap();
    config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => $dir.'/erp.sqlite', 'app.debug' => false, 'app.key' => $key, 'sanctum.stateful' => ['spa.example'], 'session.driver' => 'file', 'session.files' => $dir.'/sessions']);
    DB::purge('sqlite');

    return $app;
};
$boot();
Artisan::call('migrate', ['--force' => true]);
User::query()->create(['name' => 'کاربر', 'email' => 'spa@example.com', 'password' => 'correct horse battery', 'is_active' => true]);
$jar = [];
/** Browser-like call: keeps cookies, sends the SPA origin and optionally the XSRF header. */
$browser = function (string $method, string $uri, array $body = [], bool $xsrf = true, string $origin = 'https://spa.example') use ($boot, &$jar): array {
    $kernel = $boot()->make(HttpKernel::class);
    $server = ['HTTP_ACCEPT' => 'application/json', 'CONTENT_TYPE' => 'application/json', 'HTTP_ORIGIN' => $origin, 'HTTP_REFERER' => $origin.'/', 'REMOTE_ADDR' => '10.0.0.7', 'HTTPS' => 'on', 'HTTP_HOST' => 'erp.example'];
    if ($xsrf && isset($jar['XSRF-TOKEN'])) {
        $server['HTTP_X_XSRF_TOKEN'] = $jar['XSRF-TOKEN'];
    }
    $request = Request::create('https://erp.example'.$uri, $method, [], $jar, [], $server, $body === [] ? null : json_encode($body, JSON_THROW_ON_ERROR));
    $response = $kernel->handle($request);
    $kernel->terminate($request, $response);
    foreach ($response->headers->getCookies() as $cookie) {
        if ($cookie->isCleared()) {
            unset($jar[$cookie->getName()]);
        } else {
            $jar[$cookie->getName()] = (string) $cookie->getValue();
        }
    }

    return [$response->getStatusCode(), json_decode((string) $response->getContent(), true)];
};
$credentials = ['email' => 'spa@example.com', 'password' => 'correct horse battery'];
try {
    sessionCheck($browser('GET', '/sanctum/csrf-cookie')[0] === 204 && isset($jar['XSRF-TOKEN']), 'CSRF cookie endpoint must issue XSRF-TOKEN');
    $sessionCookie = (string) config('session.cookie');
    sessionCheck(isset($jar[$sessionCookie]), 'CSRF cookie endpoint must start a session');
    [$status] = $browser('POST', '/api/core/auth/login', $credentials, false);
    sessionCheck($status === 419, 'Stateful login without the XSRF header must be rejected, got '.$status);
    $before = $jar[$sessionCookie];
    [$status, $login] = $browser('POST', '/api/core/auth/login', $credentials);
    sessionCheck($status === 200 && $login['ok'] === true && ! isset($login['data']['token']) && $login['data']['user']['email'] === 'spa@example.com', 'Session login must succeed without issuing a token: '.json_encode($login, JSON_UNESCAPED_UNICODE));
    sessionCheck($jar[$sessionCookie] !== $before, 'Login must regenerate the session id (fixation protection)');
    [$status, $me] = $browser('GET', '/api/core/auth/me');
    sessionCheck($status === 200 && $me['data']['user']['email'] === 'spa@example.com', 'Session cookie must authenticate /me');
    [$status] = $browser('GET', '/api/core/auth/me', [], true, 'https://evil.example');
    sessionCheck($status === 401, 'A non-stateful origin must not ride the session cookie, got '.$status);
    [$status] = $browser('POST', '/api/core/auth/logout', [], false);
    sessionCheck($status === 419, 'Stateful logout without the XSRF header must be rejected');
    [$status] = $browser('POST', '/api/core/auth/logout');
    sessionCheck($status === 200, 'Session logout must succeed');
    [$status] = $browser('GET', '/api/core/auth/me');
    sessionCheck($status === 401, 'Logout must invalidate the session, got '.$status);
    sessionCheck(DB::table('core_personal_access_tokens')->count() === 0, 'Session login must not create API tokens');
    echo "SPA session login with CSRF, origin checks, fixation protection and logout tests passed.\n";
} finally {
    foreach (glob($dir.'/sessions/*') ?: [] as $file) {
        unlink($file);
    }
    rmdir($dir.'/sessions');
    unlink($dir.'/erp.sqlite');
    rmdir($dir);
}
