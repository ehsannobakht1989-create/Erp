<?php

declare(strict_types=1);

require dirname(__DIR__, 2).'/tools/architecture/Checker.php';

use Erp\Architecture\Checker;

function archCheck(bool $condition, string $message): void
{
    if (! $condition) {
        throw new RuntimeException($message);
    }
}
$root = sys_get_temp_dir().'/erp-arch-'.bin2hex(random_bytes(6));
$files = [
    'modules/alpha/src/Services/BadService.php' => <<<'PHP'
<?php
namespace Modules\Alpha\Services;
use Modules\Beta\Services\SecretService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
final class BadService {
    public function run(string $name, int $id): void {
        DB::table('beta__items')->where('id', $id)->update(['name' => $name]);
        DB::select("select * from alpha__items where name = '$name'");
        DB::table('alpha__items')->whereRaw('name = '.$name)->get();
        DB::statement(sprintf('drop table %s', $name));
        DB::select('select * from core_users');
        $this->model->saveQuietly();
        Route::get('/sneaky', fn () => 'x');
        // TODO finish later
    }
}
PHP,
    'modules/alpha/src/Models/Item.php' => "<?php\nnamespace Modules\\Alpha\\Models;\nuse Illuminate\\Database\\Eloquent\\Model;\nfinal class Item extends Model { protected \$table = 'items'; }\n",
    'modules/alpha/routes/api.php' => "<?php\nuse Illuminate\\Support\\Facades\\Route;\nRoute::get('items', fn () => []);\nRoute::post('items', fn () => [])->middleware('can:alpha.item.create');\n",
    'modules/beta/src/Contracts/BetaContract.php' => "<?php\nnamespace Modules\\Beta\\Contracts;\ninterface BetaContract { public function total(int \$companyId): string; }\n",
    'modules/beta/src/Models/Item.php' => "<?php\nnamespace Modules\\Beta\\Models;\nuse App\\Support\\Audit\\Auditable;\nuse Illuminate\\Database\\Eloquent\\Model;\nfinal class Item extends Model { use Auditable; protected \$table = 'beta__items'; protected function auditModule(): string { return 'beta'; } }\n",
    'modules/beta/src/Services/GoodService.php' => <<<'PHP'
<?php
namespace Modules\Beta\Services;
use App\Support\Contracts\AuditContract;
use Modules\Alpha\Contracts\AlphaContract;
use Illuminate\Support\Facades\DB;
final class GoodService {
    public function __construct(private AlphaContract $alpha, private AuditContract $audit) {}
    public function rename(int $id, string $name): void {
        DB::table('beta__items')->where('id', $id)->update(['name' => $name]);
        $this->audit->record(1, null, 'beta', 'beta__items', 'updated', $id, null, ['name' => $name]);
        DB::select('select * from beta__items where name = ?', [$name]);
        DB::table('beta__items')->whereRaw('lower(name) = ?', [strtolower($name)])->get();
        collect(['a', 'b'])->join(', ');
    }
}
PHP,
    'modules/beta/database/migrations/2026_01_01_000000_create_beta_items.php' => "<?php\nuse Illuminate\\Support\\Facades\\Schema;\nSchema::create('beta__items', function (\$table) { \$table->id(); });\n",
    'modules/beta/routes/api.php' => "<?php\nuse Illuminate\\Support\\Facades\\Route;\nRoute::middleware('can:beta.item.view')->group(function () {\n    Route::get('items', fn () => []);\n});\nRoute::post('items', fn () => [])->can('beta.item.create');\n",
    'modules/beta/backend/src/Legacy.php' => "<?php\n// TODO legacy code is excluded\nDB::select(\"select * from x where a = \$b\");\n",
    'app/Support/Unsafe.php' => "<?php\nDB::table('x')->whereRaw('a = '.\$value);\n// arch-allow-raw: identifier is validated\nDB::table('x')->whereRaw(\$grammar->wrap(\$column).' = ?', [\$value]);\n",
];
try {
    foreach ($files as $path => $content) {
        @mkdir(dirname($root.'/'.$path), 0777, true);
        file_put_contents($root.'/'.$path, $content);
    }
    $violations = (new Checker($root))->run();
    $text = implode("\n", $violations);
    foreach ([
        "imports another module's non-contract class Modules\\Beta\\Services\\SecretService",
        'BadService.php:8: query-builder/bulk write without explicit AuditContract',
        'BadService.php:8: table beta__items is outside prefix alpha__',
        'BadService.php:9: string-built SQL passed to select()',
        'BadService.php:10: string-built SQL passed to whereRaw()',
        'BadService.php:11: string-built SQL passed to statement()',
        'BadService.php:12: SQL references table core_users outside prefix alpha__',
        'BadService.php:13: saveQuietly bypasses audit',
        'BadService.php:14: module routes must be declared in routes/api.php',
        "BadService.php: banned marker 'TODO'",
        'Models/Item.php: Eloquent model must use App\\Support\\Audit\\Auditable',
        'model table items is outside prefix alpha__',
        'alpha/routes/api.php:3: route statement lacks a can: permission',
        'app/Support/Unsafe.php:2: string-built SQL passed to whereRaw()',
    ] as $expected) {
        archCheck(str_contains($text, $expected), "Missing architecture violation: $expected\n$text");
    }
    archCheck(! str_contains($text, 'modules/beta/'), "Compliant module must pass:\n$text");
    archCheck(! str_contains($text, 'alpha/routes/api.php:4') && ! str_contains($text, 'Unsafe.php:4'), "Guarded route and annotated core raw SQL must pass:\n$text");
    archCheck(count($violations) === 14, 'Unexpected violation count '.count($violations).":\n$text");
    archCheck((new Checker(dirname(__DIR__, 2)))->run() === [], 'Repository must have no architecture violations');
    echo "Architecture rule detection tests passed.\n";
} finally {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
    foreach ($iterator as $file) {
        $file->isDir() ? rmdir($file->getPathname()) : unlink($file->getPathname());
    }
    rmdir($root);
}
