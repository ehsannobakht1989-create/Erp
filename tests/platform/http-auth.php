<?php

declare(strict_types=1);

require dirname(__DIR__, 2).'/vendor/autoload.php';

use App\Models\User;
use Illuminate\Contracts\Console\Kernel as ConsoleKernel;
use Illuminate\Contracts\Http\Kernel as HttpKernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

function httpCheck(bool $condition, string $message): void
{
    if (! $condition) {
        throw new RuntimeException($message);
    }
}
foreach (['CACHE_STORE' => 'array', 'SESSION_DRIVER' => 'array'] as $name => $value) {
    putenv($name.'='.$value);
    $_ENV[$name] = $_SERVER[$name] = $value;
}
$app = require dirname(__DIR__, 2).'/bootstrap/app.php';
$app->make(ConsoleKernel::class)->bootstrap();
config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'cache.default' => 'array', 'app.debug' => false, 'app.key' => 'base64:'.base64_encode(random_bytes(32))]);
DB::purge('sqlite');
Artisan::call('migrate', ['--force' => true]);
httpCheck(DB::getSchemaBuilder()->hasTable('core_migrations') && ! DB::getSchemaBuilder()->hasTable('migrations'), 'Migration repository must use core prefix');
$kernel = $app->make(HttpKernel::class);
/** @return array{0:int,1:array<string,mixed>} */
function call(HttpKernel $kernel, string $method, string $uri, array $body = [], ?string $token = null): array
{
    $request = Request::create($uri, $method, [], [], [], ['HTTP_ACCEPT' => 'application/json', 'REMOTE_ADDR' => '10.0.0.1', 'CONTENT_TYPE' => 'application/json'], json_encode($body, JSON_THROW_ON_ERROR));
    if ($token !== null) {
        $request->headers->set('Authorization', 'Bearer '.$token);
    }
    $response = $kernel->handle($request);
    $kernel->terminate($request, $response);
    $json = json_decode((string) $response->getContent(), true);
    httpCheck(is_array($json) && array_keys($json) === ['ok', 'data', 'error', 'meta'], 'Every API response must use the envelope: '.$response->getContent());

    return [$response->getStatusCode(), $json];
}
User::query()->create(['name' => 'Admin', 'email' => 'Admin@Example.com', 'password' => 'correct horse battery', 'is_active' => true]);
User::query()->create(['name' => 'Inactive', 'email' => 'inactive@example.com', 'password' => 'correct horse battery', 'is_active' => false]);
httpCheck(User::query()->where('email', 'admin@example.com')->value('password') !== 'correct horse battery', 'Passwords must be hashed');
[$status, $json] = call($kernel, 'GET', '/api/core/auth/me');
httpCheck($status === 401 && $json['error']['code'] === 'unauthenticated' && $json['error']['message'] === 'احراز هویت لازم است.', 'Anonymous me must be a Persian 401 envelope');
[$status, $json] = call($kernel, 'POST', '/api/core/auth/login', []);
httpCheck($status === 422 && $json['error']['details']['fields']['email'][0] === 'ایمیل الزامی است.', 'Validation messages must be Persian');
[$status, $json] = call($kernel, 'POST', '/api/core/auth/login', ['email' => 'inactive@example.com', 'password' => 'correct horse battery', 'device_name' => 'test']);
httpCheck($status === 422 && ! isset($json['data']['token']), 'Inactive users must not log in');
[$status, $json] = call($kernel, 'POST', '/api/core/auth/login', ['email' => 'admin@example.com', 'password' => 'correct horse battery']);
httpCheck($status === 422 && isset($json['error']['details']['fields']['device_name']), 'Sessionless login requires a token device name');
[$status, $json] = call($kernel, 'POST', '/api/core/auth/login', ['email' => ' ADMIN@example.com ', 'password' => 'correct horse battery', 'device_name' => 'ci']);
httpCheck($status === 200 && $json['ok'] === true && is_string($json['data']['token']) && ! isset($json['data']['user']['password']), 'Valid login must issue token without secrets');
$token = $json['data']['token'];
httpCheck(DB::table('core_personal_access_tokens')->count() === 1 && DB::table('core_personal_access_tokens')->value('token') !== explode('|', $token)[1], 'Tokens must be stored hashed in core table');
[$status, $json] = call($kernel, 'GET', '/api/core/auth/me', [], $token);
httpCheck($status === 200 && $json['data']['user']['email'] === 'admin@example.com', 'Token must authenticate me');
[$status] = call($kernel, 'POST', '/api/core/auth/logout', [], $token);
$app['auth']->forgetGuards();
[$after] = call($kernel, 'GET', '/api/core/auth/me', [], $token);
httpCheck($status === 200 && $after === 401 && DB::table('core_personal_access_tokens')->count() === 0, 'Logout must revoke token');
Route::middleware('api')->get('/api/core-test/fail', fn () => throw new RuntimeException('secret internal detail'));
[$status, $json] = call($kernel, 'GET', '/api/core-test/fail');
httpCheck($status === 500 && $json['error']['code'] === 'server_error' && ! str_contains(json_encode($json, JSON_THROW_ON_ERROR), 'secret internal detail'), 'Internal errors must not leak');
for ($attempt = 0; $attempt < 6; $attempt++) {
    [$status, $json] = call($kernel, 'POST', '/api/core/auth/login', ['email' => 'brute@example.com', 'password' => 'wrong', 'device_name' => 'x']);
}
httpCheck($status === 429 && $json['error']['code'] === 'too_many_requests', 'Login must be rate limited with an envelope');
echo "Core HTTP auth and envelope tests passed.\n";
