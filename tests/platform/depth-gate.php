<?php

declare(strict_types=1);

require dirname(__DIR__, 2).'/tools/depth/PhpAnalyzer.php';
require dirname(__DIR__, 2).'/tools/depth/SpecParser.php';
require dirname(__DIR__, 2).'/tools/depth/Inspector.php';

use Erp\Depth\Inspector;
use Erp\Depth\SpecParser;

function depthCheck(bool $condition, string $message): void
{
    if (! $condition) {
        throw new RuntimeException($message);
    }
}

$parsed = SpecParser::parse("# x\n**Entities** Order (doc){number,lines[{item,qty}],params{a,b},due_at?}; Line{order_id,planned_setup/run,bom_id+revision}\n**Rules** totals balance; dates (start; end) ordered\n**Events out** order.created|approved{id}, stock.posted. **In** other.thing.done, unknown.a.b, *.item.used\n**Service contract** OrderService: find(id), approve(id, reason)\n**Permissions** shop.order.view|approve, other.x.view\n**AI** summarize (x), forecast hook", 'shop', ['shop', 'other']);
depthCheck(array_column($parsed['entities'], 'name') === ['Order', 'Line'] && $parsed['entities'][0]['fields'] === ['number', 'lines', 'params', 'due_at'] && $parsed['entities'][1]['fields'] === ['order_id', 'planned_setup', 'planned_run', 'bom_id', 'revision'], 'Compact entities must parse nested braces and field shorthands: '.json_encode($parsed['entities']));
depthCheck($parsed['rules'] === ['totals balance', 'dates (start; end) ordered'], 'Rules must split on top-level semicolons only');
depthCheck($parsed['events_out'] === ['shop.order.created', 'shop.order.approved', 'shop.stock.posted'] && $parsed['events_in'] === ['other.thing.done', '*.item.used'], 'Events must expand alternatives, prefix own events and keep only known producers: '.json_encode([$parsed['events_out'], $parsed['events_in']]));
depthCheck($parsed['contract'] === ['find', 'approve'] && $parsed['permissions'] === ['shop.order.view', 'shop.order.approve'] && $parsed['ai'] === ['summarize', 'forecast'], 'Contract, own permissions and AI capabilities must parse');

$root = sys_get_temp_dir().'/erp-depth-gate-'.bin2hex(random_bytes(6));
$files = [
    'specs/acme.md' => "# acme — Fixture (L1)\n\n**Entities** Widget{code,name,qty}\n**Rules** quantity must be positive; archived widgets are read-only\n**Events out** widget.created{id}. **In** other.thing.happened (sync)\n**Service contract** AcmeService: findWidget(id)\n**Permissions** acme.widget.view|create\n**Screens** widget list\n**AI** summarize\n",
    'modules/acme/module.json' => json_encode(['id' => 'acme', 'status' => 'done', 'permissions' => ['acme.widget.view', 'acme.widget.create'], 'emits' => ['acme.widget.created'], 'listens' => ['other.thing.happened'], 'entities' => ['widget'], 'ai' => ['enabled_default' => false, 'capabilities' => [['id' => 'summarize', 'provider' => 'avalai', 'tools' => ['acme.widget_lookup']]]]], JSON_THROW_ON_ERROR),
    'modules/other/module.json' => '{"id":"other","status":"stub"}',
    'modules/acme/database/migrations/2026_01_01_000000_create_acme_widgets.php' => "<?php\nuse Illuminate\\Database\\Migrations\\Migration;\nuse Illuminate\\Database\\Schema\\Blueprint;\nuse Illuminate\\Support\\Facades\\Schema;\nreturn new class extends Migration\n{\n    public function up(): void\n    {\n        Schema::create('acme__widgets', function (Blueprint \$table): void {\n            \$table->id();\n            \$table->string('code');\n            \$table->string('name');\n            \$table->decimal('qty', 20, 4);\n        });\n    }\n\n    public function down(): void\n    {\n    }\n};\n",
    'modules/acme/src/Contracts/AcmeServiceInterface.php' => "<?php\nnamespace Modules\\Acme\\Contracts;\ninterface AcmeServiceInterface\n{\n    public function findWidget(int \$companyId, int \$id): array;\n}\n",
    'modules/acme/src/Services/AcmeService.php' => "<?php\nnamespace Modules\\Acme\\Services;\nuse App\\Support\\Events\\DomainEvent;\nuse Illuminate\\Support\\Facades\\DB;\nuse Modules\\Acme\\Contracts\\AcmeServiceInterface;\nfinal class AcmeService implements AcmeServiceInterface\n{\n    public function findWidget(int \$companyId, int \$id): array\n    {\n        \$row = DB::table('acme__widgets')->where(['company_id' => \$companyId, 'id' => \$id])->first();\n        if (\$row === null) {\n            throw new \\InvalidArgumentException('Widget not found.');\n        }\n\n        return (array) \$row;\n    }\n\n    public function created(int \$companyId, int \$id): DomainEvent\n    {\n        return DomainEvent::create('acme.widget.created', \$companyId, 'widget', (string) \$id, ['id' => \$id]);\n    }\n}\n",
    'modules/acme/src/Listeners/OtherThingHandler.php' => "<?php\nnamespace Modules\\Acme\\Listeners;\nuse App\\Support\\Events\\DomainEvent;\nuse App\\Support\\Events\\DomainEventHandler;\nuse Illuminate\\Support\\Facades\\DB;\nfinal class OtherThingHandler implements DomainEventHandler\n{\n    public function handle(DomainEvent \$event): void\n    {\n        DB::table('acme__widgets')->updateOrInsert(['code' => \$event->entityId], ['name' => 'synced']);\n    }\n}\n",
    'modules/acme/src/Ai/WidgetLookup.php' => "<?php\nnamespace Modules\\Acme\\Ai;\nuse App\\Support\\Ai\\AiTool;\nuse Illuminate\\Support\\Facades\\DB;\nfinal class WidgetLookup implements AiTool\n{\n    public function describe(): array\n    {\n        return ['description' => 'Find a widget by code', 'parameters' => ['type' => 'object']];\n    }\n\n    public function run(int \$companyId, int \$userId, array \$arguments): mixed\n    {\n        return DB::table('acme__widgets')->where(['company_id' => \$companyId, 'code' => (string) (\$arguments['code'] ?? '')])->first();\n    }\n}\n",
    'modules/acme/Providers/ModuleServiceProvider.php' => "<?php\nnamespace Modules\\Acme\\Providers;\nuse Modules\\Acme\\Ai\\WidgetLookup;\nuse Modules\\Acme\\Listeners\\OtherThingHandler;\nfinal class ModuleServiceProvider extends \\App\\Support\\Modules\\ModuleServiceProvider\n{\n    protected function listeners(): array\n    {\n        return ['on_other' => ['event' => 'other.thing.happened', 'handler' => OtherThingHandler::class]];\n    }\n\n    protected function aiTools(): array\n    {\n        return ['acme.widget_lookup' => ['permission' => 'acme.widget.view', 'handler' => WidgetLookup::class]];\n    }\n}\n",
    'modules/acme/routes/api.php' => "<?php\nuse Illuminate\\Support\\Facades\\Route;\nuse Modules\\Acme\\Http\\WidgetController;\nRoute::get('widgets', [WidgetController::class, 'index'])->middleware('can:acme.widget.view');\nRoute::post('widgets/{id}/approve', [WidgetController::class, 'approve'])->middleware('can:acme.widget.create');\n",
    'modules/acme/tests/WidgetTest.php' => "<?php\nnamespace Modules\\Acme\\Tests;\nuse PHPUnit\\Framework\\TestCase;\nfinal class WidgetTest extends TestCase\n{\n    public function test_rule_quantity_positive_rejects_negative(): void\n    {\n        \$this->assertSame(1, 1);\n        \$this->assertTrue(true);\n    }\n\n    public function test_rule_archived_widgets_are_read_only(): void\n    {\n        self::assertSame('a', 'a');\n        \$this->expectException(\\LogicException::class);\n        throw new \\LogicException('read only');\n    }\n\n    public function test_ai_summarize_off_returns_disabled(): void\n    {\n        \$this->assertSame('disabled', 'disabled');\n    }\n\n    public function test_ai_summarize_fake_provider_returns_output(): void\n    {\n        \$this->assertNotNull('x');\n    }\n}\n",
    'modules/acme/web/routes.ts' => "import WidgetListPage from './pages/WidgetListPage';\nimport WidgetFormPage from './pages/WidgetFormPage';\nimport WidgetDetailPage from './pages/WidgetDetailPage';\nexport default [\n  { path: '/acme/widgets', component: WidgetListPage, permission: 'acme.widget.view' },\n  { path: '/acme/widgets/new', component: WidgetFormPage, permission: 'acme.widget.create' },\n  { path: '/acme/widgets/:id', component: WidgetDetailPage, permission: 'acme.widget.view' },\n];\n",
    'modules/acme/web/pages/WidgetListPage.tsx' => "import { t } from '@erp/i18n';\nexport default function WidgetListPage() {\n  return <input placeholder=\"code\" aria-label={t('widget.title')} />;\n}\n",
    'modules/acme/web/pages/WidgetFormPage.tsx' => "import { t } from '@erp/i18n';\nexport default function WidgetFormPage() {\n  return <h1>{t('widget.title')}</h1>;\n}\n",
    'modules/acme/web/pages/WidgetDetailPage.tsx' => "import { t } from '@erp/i18n';\nexport default function WidgetDetailPage() {\n  return <h1>{t('widget.title')}</h1>;\n}\n",
    'modules/acme/web/i18n/fa.json' => '{"title": "نمونه", "widget": {"title": "ویجت"}}',
    'modules/bare/module.json' => '{"id":"bare","status":"partial"}',
    'fake-phpunit.php' => "<?php\n\$ok = (\$argv[1] ?? '') === 'modules/acme/tests' && getenv('FAKE_PHPUNIT_FAIL') !== '1';\necho \$ok ? \"OK (4 tests, 6 assertions)\\n\" : \"FAILURES!\\nTests: 4, Failures: 1.\\n\";\nexit(\$ok ? 0 : 1);\n",
];
$write = function (string $path, string $content) use ($root): void {
    @mkdir(dirname($root.'/'.$path), 0777, true);
    file_put_contents($root.'/'.$path, $content);
};
$cli = function (array $arguments) use ($root): array {
    // @phpstan-ignore argument.type (PHP 7.4+ documents the ['redirect', 1] descriptor)
    $process = proc_open([PHP_BINARY, dirname(__DIR__, 2).'/tools/depth-check.php', '--root='.$root, '--phpunit='.$root.'/fake-phpunit.php', ...$arguments], [1 => ['pipe', 'w'], 2 => ['redirect', 1]], $pipes);
    $output = (string) stream_get_contents($pipes[1]);
    fclose($pipes[1]);

    return [proc_close($process), $output];
};
try {
    foreach ($files as $path => $content) {
        $write($path, $content);
    }
    mkdir($root.'/docs');
    $inspector = fn () => new Inspector($root, [PHP_BINARY, $root.'/fake-phpunit.php']);
    $report = $inspector()->inspect('acme', true);
    foreach ($report['categories'] as $category => $result) {
        depthCheck($result['percent'] === 100.0, "$category must be complete for the compliant fixture: ".json_encode($result, JSON_UNESCAPED_UNICODE));
    }
    depthCheck($report['score'] === 100.0 && $report['violations'] === [] && $report['tests']['status'] === 'passed' && $report['pass'] && $report['suggested_status'] === 'done', 'Compliant fixture must pass: '.json_encode($report, JSON_UNESCAPED_UNICODE));
    depthCheck($inspector()->inspect('acme')['tests']['status'] === 'not_run' && ! $inspector()->inspect('acme')['pass'], 'Without test execution a module cannot pass');
    putenv('FAKE_PHPUNIT_FAIL=1');
    $failedTests = $inspector()->inspect('acme', true);
    putenv('FAKE_PHPUNIT_FAIL');
    depthCheck($failedTests['tests']['status'] === 'failed' && ! $failedTests['pass'] && str_contains($failedTests['tests']['detail'], 'exit 1'), 'Failing module tests must fail the gate');
    [$exit, $output] = $cli(['--all', '--gate', '--write-status']);
    depthCheck($exit === 1 && str_contains($output, 'GATE: bare: marked partial but has no Laravel implementation') && ! str_contains($output, 'GATE: acme'), 'Gate must accept passing done and reject partial without implementation: '.$output);
    $status = (string) file_get_contents($root.'/docs/depth-status.md');
    depthCheck(str_contains($status, '| acme | done | done | 100 | 100 | 100 | 100 | 100 | 100 | 100 | 100 | 0 | passed | yes |') && str_contains($status, '## other — stub, score 0, FAIL') && str_contains($status, 'specification missing: specs/other.md'), 'Status file must list scores and exact gaps');
    $write('modules/bare/module.json', '{"id":"bare","status":"stub"}');
    depthCheck($cli(['--all', '--gate'])[0] === 0, 'Gate must pass when every claim is honest');

    $variants = [
        'banned marker' => ['modules/acme/src/Services/AcmeService.php', fn (string $s) => str_replace('return (array) $row;', "// TODO cache\n        return (array) \$row;", $s), "banned marker 'TODO'"],
        'trivial method' => ['modules/acme/src/Services/AcmeService.php', fn (string $s) => str_replace('    public function created', "    public function noop(): array\n    {\n        return [];\n    }\n\n    public function created", $s), 'noop only returns a trivial value'],
        'empty test' => ['modules/acme/tests/WidgetTest.php', fn (string $s) => substr($s, 0, -2)."\n    public function test_nothing(): void\n    {\n        \$x = 1;\n    }\n}\n", 'test_nothing asserts nothing'],
        'skipped test' => ['modules/acme/tests/WidgetTest.php', fn (string $s) => substr($s, 0, -2)."\n    public function test_later(): void\n    {\n        \$this->markTestSkipped('x');\n        \$this->assertTrue(true);\n    }\n}\n", 'test_later is skipped'],
        'generic CRUD routes' => ['modules/acme/routes/api.php', fn (string $s) => str_replace("Route::get('widgets', [WidgetController::class, 'index'])", "Route::apiResource('widgets', WidgetController::class)", $s), '5 of 6 routes are generic CRUD'],
        'backend mock data' => ['modules/acme/src/Support/Seed.php', fn (string $s) => "<?php\nfunction rows(): array\n{\n    return \\Faker\\Factory::create()->words();\n}\n", 'mock or fake data in a backend path'],
        'trivial contract' => ['modules/acme/src/Services/AcmeService.php', fn (string $s) => (string) preg_replace('/(findWidget\(int \$companyId, int \$id\): array\n    \{\n).*?(\n    \}\n)/s', '$1        return [];$2', $s), 'findWidget only returns a trivial value'],
    ];
    foreach ($variants as $name => [$path, $mutate, $expected]) {
        $original = is_file($root.'/'.$path) ? (string) file_get_contents($root.'/'.$path) : null;
        $write($path, $mutate($original ?? ''));
        $gamed = $inspector()->inspect('acme', true);
        depthCheck(! $gamed['pass'] && $gamed['tests']['status'] === 'not_run' && str_contains(implode("\n", $gamed['violations']), $expected), "Variant '$name' must be rejected with '$expected': ".json_encode($gamed['violations']));
        $original === null ? unlink($root.'/'.$path) : $write($path, $original);
    }
    $handler = (string) file_get_contents($root.'/modules/acme/src/Listeners/OtherThingHandler.php');
    $write('modules/acme/src/Listeners/OtherThingHandler.php', str_replace("updateOrInsert(['code' => \$event->entityId], ['name' => 'synced'])", "insert(['code' => 'x', 'name' => 'synced'])", $handler));
    $event = $inspector()->inspect('acme')['categories']['event_cov'];
    depthCheck($event['percent'] === 50.0 && str_contains(implode("\n", $event['missing']), 'shows no idempotency guard'), 'Non-idempotent handlers must not count: '.json_encode($event));
    $write('modules/acme/src/Listeners/OtherThingHandler.php', $handler);
    rename($root.'/specs/acme.md', $root.'/specs/acme.off');
    $noSpec = $inspector()->inspect('acme', true);
    depthCheck($noSpec['score'] === 0.0 && ! $noSpec['pass'] && $noSpec['categories']['rule_cov']['missing'] === ['specification missing: specs/acme.md'], 'Modules without a spec must score zero');
    depthCheck($cli(['--all', '--gate'])[0] === 1, 'Done without a passing gate must fail the CLI gate');
    rename($root.'/specs/acme.off', $root.'/specs/acme.md');
    symlink('/nonexistent', $root.'/modules/acme/link.php');
    depthCheck($cli(['--all', '--gate'])[0] === 2, 'Symlinks must abort the scan');
    unlink($root.'/modules/acme/link.php');
    foreach ([[], ['--gate'], ['../acme'], ['--all', '--all'], ['missing'], ['acme', '--write-status']] as $arguments) {
        depthCheck($cli($arguments)[0] === 2, 'Invalid CLI input must exit 2: '.json_encode($arguments));
    }
    echo "Depth gate scoring, anti-gaming, test execution and CLI tests passed.\n";
} finally {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
    foreach ($iterator as $file) {
        $file->isDir() && ! $file->isLink() ? rmdir($file->getPathname()) : unlink($file->getPathname());
    }
    rmdir($root);
}
