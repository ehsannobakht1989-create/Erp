<?php

declare(strict_types=1);

require dirname(__DIR__, 2).'/vendor/autoload.php';

use Illuminate\Contracts\Console\Kernel as ConsoleKernel;
use Illuminate\Contracts\Http\Kernel as HttpKernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

function installCheck(bool $condition, string $message): void
{
    if (! $condition) {
        throw new RuntimeException($message);
    }
}
foreach (['CACHE_STORE' => 'array', 'APP_KEY' => ''] as $name => $value) {
    putenv($name.'='.$value);
    $_ENV[$name] = $_SERVER[$name] = $value;
}
$dir = sys_get_temp_dir().'/erp-install-'.bin2hex(random_bytes(6));
mkdir($dir);
$app = require dirname(__DIR__, 2).'/bootstrap/app.php';
$app->make(ConsoleKernel::class)->bootstrap();
config(['erp.install.env_path' => $dir.'/.env', 'erp.install.lock_path' => $dir.'/installed.lock', 'app.debug' => false]);
$kernel = $app->make(HttpKernel::class);
$send = function (string $method, array $data = [], array $cookies = []) use ($kernel) {
    $request = Request::create('/install', $method, $data, $cookies, [], ['REMOTE_ADDR' => '10.0.0.9']);
    $response = $kernel->handle($request);
    $kernel->terminate($request, $response);

    return $response;
};
try {
    $page = $send('GET');
    $html = (string) $page->getContent();
    installCheck($page->getStatusCode() === 200 && str_contains($html, 'dir="rtl"') && str_contains($html, 'بررسی پیش‌نیازها'), 'Installer page must render in Persian RTL');
    $cookie = null;
    foreach ($page->headers->getCookies() as $candidate) {
        if ($candidate->getName() === 'erp_install_token') {
            $cookie = $candidate;
        }
    }
    installCheck($cookie !== null && $cookie->isHttpOnly() && strtolower((string) $cookie->getSameSite()) === 'strict' && preg_match('/name="_install_token" value="'.$cookie->getValue().'"/', $html) === 1, 'Installer must issue a strict double-submit token');
    $token = $cookie->getValue();
    $valid = ['_install_token' => $token, 'db_connection' => 'sqlite', 'db_database' => $dir.'/erp.sqlite', 'app_url' => 'https://erp.example', 'admin_name' => 'مدیر', 'admin_email' => 'Admin@Example.com', 'admin_password' => 'a-strong-passphrase', 'admin_password_confirmation' => 'a-strong-passphrase'];
    installCheck($send('POST', $valid)->getStatusCode() === 419, 'Missing token cookie must be rejected');
    installCheck($send('POST', ['_install_token' => str_repeat('a', 64)] + $valid, ['erp_install_token' => $token])->getStatusCode() === 419, 'Mismatched token must be rejected');
    $invalid = $send('POST', ['admin_password' => 'short', 'admin_password_confirmation' => 'short'] + $valid, ['erp_install_token' => $token]);
    installCheck($invalid->getStatusCode() === 422 && str_contains((string) $invalid->getContent(), 'دست‌کم') && ! str_contains((string) $invalid->getContent(), 'a-strong-passphrase'), 'Validation errors must be Persian and must not echo passwords');
    installCheck(! is_file($dir.'/.env') && ! is_file($dir.'/installed.lock'), 'Failed validation must not write files');
    $done = $send('POST', $valid, ['erp_install_token' => $token]);
    installCheck($done->getStatusCode() === 200 && str_contains((string) $done->getContent(), 'schedule:run'), 'Successful install must show cron instructions: '.substr(strip_tags((string) $done->getContent()), 0, 400));
    $env = (string) file_get_contents($dir.'/.env');
    installCheck(preg_match('/^APP_KEY="base64:[A-Za-z0-9+\/=]{44}"$/m', $env) === 1 && str_contains($env, 'DB_CONNECTION="sqlite"') && str_contains($env, 'APP_DEBUG="false"'), 'Installer must write a production .env with a generated key');
    installCheck(is_file($dir.'/installed.lock'), 'Installer must lock itself');
    installCheck(Schema::hasTable('core_settings') && Schema::hasTable('core_event_inbox') && DB::table('core_users')->count() === 1, 'Migrations and super-admin creation must run');
    $admin = DB::table('core_users')->first();
    installCheck($admin->email === 'admin@example.com' && $admin->password !== 'a-strong-passphrase' && password_verify('a-strong-passphrase', $admin->password), 'Super-admin must be stored with a hashed password');
    installCheck($send('GET')->getStatusCode() === 404 && $send('POST', $valid, ['erp_install_token' => $token])->getStatusCode() === 404, 'Locked installer must be unavailable');
    unlink($dir.'/installed.lock');
    $again = $send('POST', $valid, ['erp_install_token' => $token]);
    installCheck($again->getStatusCode() === 422 && str_contains((string) $again->getContent(), 'قبلاً نصب'), 'Installer must refuse an already-installed database even without the lock');
    echo "Web installer tests passed.\n";
} finally {
    foreach (array_diff(scandir($dir) ?: [], ['.', '..']) as $file) {
        unlink($dir.'/'.$file);
    }
    rmdir($dir);
}
