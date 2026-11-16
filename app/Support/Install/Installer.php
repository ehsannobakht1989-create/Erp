<?php

declare(strict_types=1);

namespace App\Support\Install;

use App\Models\User;
use App\Support\Modules\ModuleState;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PDO;
use RuntimeException;
use Throwable;

final class Installer
{
    private const EXTENSIONS = ['pdo', 'mbstring', 'openssl', 'tokenizer', 'ctype', 'json', 'fileinfo', 'filter'];

    public function locked(): bool
    {
        return is_file((string) config('erp.install.lock_path'));
    }

    /** @return list<array{label:string, ok:bool}> */
    public function requirements(): array
    {
        $checks = [['label' => 'PHP 8.2 یا بالاتر (فعلی: '.PHP_VERSION.')', 'ok' => version_compare(PHP_VERSION, '8.2.0', '>=')]];
        foreach (self::EXTENSIONS as $extension) {
            $checks[] = ['label' => 'افزونه PHP: '.$extension, 'ok' => extension_loaded($extension)];
        }
        $checks[] = ['label' => 'درایور پایگاه داده (pdo_mysql یا pdo_sqlite)', 'ok' => extension_loaded('pdo_mysql') || extension_loaded('pdo_sqlite')];
        foreach ([storage_path(), base_path('bootstrap/cache'), dirname((string) config('erp.install.env_path'))] as $path) {
            $checks[] = ['label' => 'قابل نوشتن: '.$path, 'ok' => is_dir($path) && is_writable($path)];
        }

        return $checks;
    }

    /**
     * @param  array{db_connection:string, db_host?:string|null, db_port?:string|int|null, db_database:string, db_username?:string|null, db_password?:string|null, app_url:string, admin_name:string, admin_email:string, admin_password:string}  $input
     * @return array{admin_id:int, seeded:list<string>}
     */
    public function install(array $input): array
    {
        if ($this->locked()) {
            throw new RuntimeException('نصب قبلاً انجام شده است.');
        }
        foreach ($this->requirements() as $check) {
            if (! $check['ok']) {
                throw new RuntimeException('پیش‌نیاز برقرار نیست: '.$check['label']);
            }
        }
        $connection = $input['db_connection'];
        $database = $connection === 'sqlite'
            ? ['driver' => 'sqlite', 'database' => $input['db_database'], 'prefix' => '', 'foreign_key_constraints' => true]
            : ['driver' => 'mysql', 'host' => (string) $input['db_host'], 'port' => (string) ($input['db_port'] ?: '3306'), 'database' => $input['db_database'], 'username' => (string) $input['db_username'], 'password' => (string) ($input['db_password'] ?? ''), 'charset' => 'utf8mb4', 'collation' => 'utf8mb4_unicode_ci', 'prefix' => '', 'strict' => true, 'engine' => 'InnoDB', 'options' => [PDO::ATTR_TIMEOUT => 5]];
        if ($connection === 'sqlite' && ! is_file($input['db_database']) && @touch($input['db_database']) === false) {
            throw new RuntimeException('فایل پایگاه داده SQLite قابل ایجاد نیست.');
        }
        config(['database.connections.'.$connection => $database, 'database.default' => $connection]);
        DB::purge($connection);
        try {
            DB::connection($connection)->getPdo();
        } catch (Throwable) {
            throw new RuntimeException('اتصال به پایگاه داده برقرار نشد؛ مشخصات را بررسی کنید.');
        }
        if (Schema::hasTable('core_users') && DB::table('core_users')->exists()) {
            throw new RuntimeException('این پایگاه داده قبلاً نصب شده است.');
        }
        $key = 'base64:'.base64_encode(random_bytes(32));
        $this->writeEnv([
            'APP_NAME' => 'ERP', 'APP_ENV' => 'production', 'APP_KEY' => $key, 'APP_DEBUG' => 'false', 'APP_URL' => $input['app_url'],
            'APP_LOCALE' => 'fa', 'APP_FALLBACK_LOCALE' => 'fa', 'ERP_DISPLAY_TIMEZONE' => 'Asia/Tehran',
            'DB_CONNECTION' => $connection, 'DB_HOST' => (string) ($database['host'] ?? ''), 'DB_PORT' => (string) ($database['port'] ?? ''), 'DB_DATABASE' => $input['db_database'],
            'DB_USERNAME' => (string) ($database['username'] ?? ''), 'DB_PASSWORD' => (string) ($database['password'] ?? ''),
            'QUEUE_CONNECTION' => 'database', 'SESSION_DRIVER' => 'file', 'CACHE_STORE' => 'file', 'ERP_AI_ENABLED' => 'false',
        ]);
        config(['app.key' => $key]);
        Artisan::call('migrate', ['--force' => true]);
        $admin = User::query()->create(['name' => $input['admin_name'], 'email' => $input['admin_email'], 'password' => $input['admin_password'], 'is_active' => true]);
        config(['erp.install.admin_user_id' => (int) $admin->getKey()]);
        $seeded = [];
        foreach (app(ModuleState::class)->enabled() as $id) {
            $class = 'Modules\\'.str_replace(' ', '', ucwords(str_replace('-', ' ', $id))).'\\Database\\Seeders\\ModuleSeeder';
            if (class_exists($class)) {
                Artisan::call('db:seed', ['--class' => $class, '--force' => true]);
                $seeded[] = $id;
            }
        }
        if (file_put_contents((string) config('erp.install.lock_path'), json_encode(['installed_at' => now()->toIso8601String()], JSON_THROW_ON_ERROR)) === false) {
            throw new RuntimeException('فایل قفل نصب نوشته نشد.');
        }

        return ['admin_id' => (int) $admin->getKey(), 'seeded' => $seeded];
    }

    /** @param array<string, string> $values */
    private function writeEnv(array $values): void
    {
        $lines = ['# Generated by the ERP web installer.'];
        foreach ($values as $name => $value) {
            if (preg_match('/[\r\n\0]/', $value) === 1) {
                throw new RuntimeException('مقادیر چندخطی مجاز نیست.');
            }
            $lines[] = $name.'="'.addcslashes($value, '"\\$').'"';
        }
        $path = (string) config('erp.install.env_path');
        if (file_put_contents($path, implode("\n", $lines)."\n", LOCK_EX) === false) {
            throw new RuntimeException('فایل .env قابل نوشتن نیست.');
        }
        @chmod($path, 0600);
    }
}
