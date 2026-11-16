<?php

declare(strict_types=1);

require dirname(__DIR__, 2).'/vendor/autoload.php';

use App\Support\Ai\AiGateway;
use App\Support\Ai\AiTool;
use App\Support\Ai\AiToolRegistry;
use App\Support\Ai\AvalAiProvider;
use App\Support\Ai\PythonServiceProvider;
use App\Support\Contracts\AiGatewayContract;
use App\Support\Contracts\AuditContract;
use App\Support\Contracts\PermissionContract;
use App\Support\Modules\ModuleCatalog;
use App\Support\Tenancy\CompanyContext;
use Brick\Math\BigDecimal;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\Request as HttpRequest;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

final class StockLookupTool implements AiTool
{
    public function describe(): array
    {
        return ['description' => 'Stock on hand for an item', 'parameters' => ['type' => 'object', 'properties' => ['item' => ['type' => 'string']]]];
    }

    public function run(int $companyId, int $userId, array $arguments): mixed
    {
        return ['item' => $arguments['item'] ?? null, 'qty' => '5.0000', 'email' => 'buyer@example.com'];
    }
}
function aiCheck(bool $condition, string $message): void
{
    if (! $condition) {
        throw new RuntimeException($message);
    }
}
$app = require dirname(__DIR__, 2).'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();
config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'logging.default' => 'null']);
DB::purge('sqlite');
Artisan::call('migrate', ['--force' => true]);
$audit = new class implements AuditContract
{
    public array $actions = [];

    public function record(int $companyId, ?int $actorId, string $module, string $table, string $action, int|string $recordId, ?array $before = null, ?array $after = null): void
    {
        $this->actions[] = $action;
    }
};
$app->instance(AuditContract::class, $audit);
$app->instance(PermissionContract::class, new class implements PermissionContract
{
    public function can(int $userId, int $companyId, string $permission): bool
    {
        return $userId === 1 && $permission === 'fixture.stock.view';
    }
});
$path = sys_get_temp_dir().'/erp-ai-'.bin2hex(random_bytes(6));
mkdir($path.'/fixture', 0777, true);
file_put_contents($path.'/fixture/module.json', json_encode(['id' => 'fixture', 'requires' => [], 'optional' => [], 'permissions' => ['fixture.stock.view'], 'ai' => ['enabled_default' => false, 'capabilities' => [
    ['id' => 'assistant', 'provider' => 'avalai', 'tools' => ['fixture.stock_lookup'], 'proposals' => ['fixture.reorder'], 'redact' => ['supplier_secret'], 'max_tokens_per_call' => 200],
    ['id' => 'forecast', 'provider' => 'python'],
    ['id' => 'summarize', 'provider' => 'off'],
]]], JSON_THROW_ON_ERROR));
$catalog = ModuleCatalog::scan($path);
unlink($path.'/fixture/module.json');
rmdir($path.'/fixture');
rmdir($path);
$tools = new AiToolRegistry($catalog);
try {
    $tools->register('fixture.other_tool', 'fixture.stock.view', StockLookupTool::class);
    throw new RuntimeException('Undeclared AI tool accepted');
} catch (InvalidArgumentException) {
}
$tools->register('fixture.stock_lookup', 'fixture.stock.view', StockLookupTool::class);
$http = new Factory;
$queue = new ArrayObject;
$http->fake(function (HttpRequest $request) use ($queue) {
    $responses = $queue->getArrayCopy();
    if ($responses === []) {
        throw new RuntimeException('Unexpected HTTP call');
    }
    $next = array_shift($responses);
    $queue->exchangeArray($responses);

    return $next;
});
$secret = str_repeat('k', 40);
$config = (array) config('erp.ai');
$config['prices']['avalai'] = ['input_per_1k' => '0.5000', 'output_per_1k' => '1.5000'];
$gateway = fn (array $overrides = [], string $pythonSecret = '') => new AiGateway($catalog, ['fixture'], DB::connection(), app(CompanyContext::class), $tools, [
    'avalai' => new AvalAiProvider($http, 'https://ai.example/v1', 'test-key', 'test-model'),
    'python' => new PythonServiceProvider($http, 'https://py.example', $pythonSecret),
], array_replace($config, $overrides));
$context = app(CompanyContext::class);
$context->activate(1);
$input = ['question' => 'آیا سفارش لازم است؟', 'supplier_secret' => 'S3CR3T', 'nested' => ['password' => 'hunter2']];
aiCheck(app(AiGatewayContract::class) instanceof AiGateway && app(AiGatewayContract::class)->invoke(1, 1, 'fixture', 'assistant', $input)['status'] === 'disabled', 'Default gateway must be off');
aiCheck($gateway()->invoke(1, 1, 'fixture', 'assistant', $input)['status'] === 'disabled', 'Factory level must default off');
$on = $gateway(['enabled' => true]);
aiCheck($on->invoke(1, 1, 'fixture', 'assistant', $input)['status'] === 'disabled', 'Module default must be off');
$on->setToggle(1, 'fixture', null, true, 9);
aiCheck($on->invoke(1, 1, 'fixture', 'assistant', $input)['status'] === 'disabled', 'Capability must default off');
$on->setToggle(1, 'fixture', 'assistant', true, 9);
$on->setToggle(1, 'fixture', 'summarize', true, 9);
aiCheck($gateway(['enabled' => true, 'kill_switch' => true])->invoke(1, 1, 'fixture', 'assistant', $input)['status'] === 'disabled', 'Kill switch must disable AI');
aiCheck($on->invoke(1, 1, 'fixture', 'summarize', $input)['status'] === 'disabled', 'Provider off must stay disabled');
aiCheck($on->invoke(1, 1, 'fixture', 'assistant', $input)['status'] === 'budget_exceeded', 'Missing budget must block external calls');
aiCheck(DB::table('core_ai_usage')->count() === 0 && $http->recorded()->isEmpty(), 'Blocked calls must not reach providers or record usage');
$on->setMonthlyBudget(1, '1.0000', 9);
$queue->exchangeArray([
    $http->response(['choices' => [['message' => ['content' => null, 'tool_calls' => [['id' => 'c1', 'type' => 'function', 'function' => ['name' => 'fixture__stock_lookup', 'arguments' => '{"item":"bolt"}']]]]]], 'usage' => ['prompt_tokens' => 100, 'completion_tokens' => 10]]),
    $http->response(['choices' => [['message' => ['content' => json_encode(['output' => 'سفارش پیشنهاد می‌شود', 'proposals' => [['type' => 'fixture.reorder', 'payload' => ['item' => 'bolt', 'qty' => '10.0000']]]])]]], 'usage' => ['prompt_tokens' => 200, 'completion_tokens' => 50]]),
]);
$result = $on->invoke(1, 1, 'fixture', 'assistant', $input);
aiCheck($result['ok'] === true && $result['output'] === 'سفارش پیشنهاد می‌شود' && count($result['proposal_ids']) === 1, 'Enabled call must return output and pending proposal');
[$first, $second] = array_map(fn (array $pair) => $pair[0], $http->recorded()->all());
foreach ([$first, $second] as $sent) {
    aiCheck(! str_contains($sent->body(), 'S3CR3T') && ! str_contains($sent->body(), 'hunter2') && $sent->hasHeader('Authorization', 'Bearer test-key'), 'Redaction and auth must apply to every external request');
}
aiCheck($first['max_tokens'] === 200 && $first['tools'][0]['function']['name'] === 'fixture__stock_lookup', 'Capability token limit and manifest tools must be sent');
aiCheck(str_contains($second->body(), '5.0000') && ! str_contains($second->body(), 'buyer@example.com'), 'Tool results must be permission-run and redacted');
$usage = DB::table('core_ai_usage')->first();
aiCheck($usage->status === 'succeeded' && (int) $usage->input_tokens === 300 && (int) $usage->output_tokens === 60 && BigDecimal::of((string) $usage->cost)->isEqualTo('0.24'), 'Usage tokens and exact cost must be recorded: '.$usage->cost);
aiCheck(array_slice($audit->actions, -2) === ['ai_call_started', 'ai_call_succeeded'], 'Every external call must be audited');
$proposal = DB::table('core_ai_proposals')->first();
aiCheck($proposal->status === 'pending' && json_decode($proposal->payload, true)['qty'] === '10.0000', 'AI output must only create pending proposals');
$on->decideProposal((int) $proposal->id, true, 9);
aiCheck(DB::table('core_ai_proposals')->value('status') === 'accepted' && end($audit->actions) === 'accepted', 'Proposal decision must be recorded and audited');
try {
    $on->decideProposal((int) $proposal->id, false, 9);
    throw new RuntimeException('Decided proposal changed again');
} catch (InvalidArgumentException) {
}
$queue->exchangeArray([
    $http->response(['choices' => [['message' => ['content' => null, 'tool_calls' => [['id' => 'c2', 'type' => 'function', 'function' => ['name' => 'fixture__stock_lookup', 'arguments' => '{"item":"nut"}']]]]]]]),
    $http->response(['choices' => [['message' => ['content' => '{"output":"بدون دسترسی"}']]]]),
]);
aiCheck($on->invoke(1, 2, 'fixture', 'assistant', ['question' => 'x'])['ok'] === true && str_contains($http->recorded()[3][0]->body(), 'forbidden') && ! str_contains($http->recorded()[3][0]->body(), '5.0000'), 'Tools must use the invoking user permissions');
$queue->exchangeArray([$http->response(['choices' => [['message' => ['content' => null, 'tool_calls' => [['id' => 'c3', 'type' => 'function', 'function' => ['name' => 'fixture__drop_tables', 'arguments' => '{}']]]]]]])]);
$failed = $on->invoke(1, 1, 'fixture', 'assistant', ['question' => 'x']);
aiCheck($failed['status'] === 'failed' && ! str_contains(json_encode($failed, JSON_THROW_ON_ERROR), 'drop_tables'), 'Undeclared tool must fail without leaking internals');
$queue->exchangeArray([$http->response(['choices' => [['message' => ['content' => '{"output":"x","proposals":[{"type":"fixture.delete_all","payload":{}}]}']]]])]);
aiCheck($on->invoke(1, 1, 'fixture', 'assistant', ['question' => 'x'])['status'] === 'failed' && DB::table('core_ai_proposals')->count() === 1, 'Undeclared proposal types must be rejected atomically');
aiCheck(DB::table('core_ai_usage')->where('status', 'failed')->count() === 2, 'Failed calls must be recorded');
$on->setToggle(1, 'fixture', 'forecast', true, 9);
aiCheck($on->invoke(1, 1, 'fixture', 'forecast', ['series' => [1, 2]])['status'] === 'failed', 'Unconfigured Python secret must fail closed');
$python = $gateway(['enabled' => true], $secret);
$queue->exchangeArray([$http->response(['output' => ['next' => 3], 'usage' => ['input_tokens' => 0, 'output_tokens' => 0]])]);
aiCheck($python->invoke(1, 1, 'fixture', 'forecast', ['series' => [1, 2], 'email' => 'x@y.z'])['output'] === ['next' => 3], 'Python capability must return output');
$sent = $http->recorded()->last()[0];
$expected = PythonServiceProvider::signature($secret, $sent->header('X-ERP-Timestamp')[0], $sent->header('X-ERP-Request-Id')[0], $sent->body());
aiCheck($sent->url() === 'https://py.example/v1/capabilities/fixture/forecast' && hash_equals($expected, $sent->header('X-ERP-Signature')[0]) && ! str_contains($sent->body(), 'x@y.z'), 'Python requests must be HMAC signed and redacted');
$on->setMonthlyBudget(1, '0.2400', 9);
aiCheck($on->invoke(1, 1, 'fixture', 'assistant', $input)['status'] === 'budget_exceeded', 'Spent budget must block calls');
try {
    $on->invoke(2, 1, 'fixture', 'assistant', $input);
    throw new RuntimeException('Cross-company AI call accepted');
} catch (LogicException) {
}
try {
    $on->setToggle(1, 'fixture', 'ocr', true, 9);
    throw new RuntimeException('Undeclared capability toggle accepted');
} catch (InvalidArgumentException) {
}
echo "AI gateway enablement, redaction, tools, proposals, budget and HMAC tests passed.\n";
