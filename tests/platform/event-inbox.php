<?php

declare(strict_types=1);

require dirname(__DIR__, 2).'/vendor/autoload.php';

use App\Support\Events\DomainEvent;
use App\Support\Events\DomainEventBus;
use App\Support\Events\DomainEventHandler;
use App\Support\Events\EventSubscriptions;
use App\Support\Events\InboxProcessor;
use App\Support\Modules\ModuleCatalog;
use App\Support\Tenancy\CompanyContext;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

final class ConsumerHandler implements DomainEventHandler
{
    public static int $failuresLeft = 0;

    public static ?int $seenCompany = null;

    public function handle(DomainEvent $event): void
    {
        self::$seenCompany = app(CompanyContext::class)->companyId();
        DB::table('consumer_effects')->insert(['event_id' => $event->eventId, 'company_id' => self::$seenCompany]);
        if (self::$failuresLeft-- > 0) {
            throw new RuntimeException('transient failure');
        }
    }
}
final class NotAHandler {}
function inboxCheck(bool $condition, string $message): void
{
    if (! $condition) {
        throw new RuntimeException($message);
    }
}
function inboxRejects(callable $call, string $message): void
{
    try {
        $call();
    } catch (InvalidArgumentException|LogicException) {
        return;
    }
    throw new RuntimeException($message);
}
$app = require dirname(__DIR__, 2).'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();
config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'queue.default' => 'database', 'logging.default' => 'null']);
DB::purge('sqlite');
Artisan::call('migrate', ['--force' => true]);
Schema::create('consumer_effects', function (Blueprint $table): void {
    $table->string('event_id');
    $table->unsignedBigInteger('company_id');
});
$path = sys_get_temp_dir().'/erp-inbox-'.bin2hex(random_bytes(6));
foreach (['producer' => ['emits' => ['producer.record.created'], 'listens' => []], 'consumer' => ['emits' => [], 'listens' => ['producer.record.created']]] as $id => $events) {
    mkdir($path.'/'.$id, 0777, true);
    file_put_contents($path.'/'.$id.'/module.json', json_encode(['id' => $id, 'requires' => [], 'optional' => []] + $events, JSON_THROW_ON_ERROR));
}
$subscriptions = new EventSubscriptions(ModuleCatalog::scan($path));
foreach (['producer', 'consumer'] as $id) {
    unlink($path.'/'.$id.'/module.json');
    rmdir($path.'/'.$id);
}
rmdir($path);
inboxRejects(fn () => $subscriptions->async('producer.record.created', 'producer.self', ConsumerHandler::class), 'Undeclared listens must be rejected');
inboxRejects(fn () => $subscriptions->async('producer.record.created', 'Consumer Bad', ConsumerHandler::class), 'Invalid listener name accepted');
// @phpstan-ignore argument.type (deliberate negative test)
inboxRejects(fn () => $subscriptions->async('producer.record.created', 'consumer.bad', NotAHandler::class), 'Non-handler accepted');
$subscriptions->async('producer.record.created', 'consumer.on_record', ConsumerHandler::class);
inboxRejects(fn () => $subscriptions->async('producer.record.created', 'consumer.on_record', ConsumerHandler::class), 'Duplicate listener accepted');
$app->instance(EventSubscriptions::class, $subscriptions);
$context = app(CompanyContext::class);
$context->activate(1);
$bus = app(DomainEventBus::class);
$event = fn () => DomainEvent::create('producer.record.created', 1, 'record', '10', ['qty' => '1.0000'], 5);
try {
    DB::transaction(function () use ($bus, $event): void {
        $bus->publish($event());
        throw new DomainException('rollback');
    });
} catch (Throwable $e) {
    inboxCheck($e instanceof DomainException, 'Unexpected rollback error');
}
inboxCheck(DB::table('core_event_inbox')->count() === 0 && DB::table('core_jobs')->count() === 0, 'Rollback must drop inbox rows and queue triggers');
$first = $event();
DB::transaction(function () use ($bus, $first): void {
    $bus->publish($first);
    $bus->publish($first);
    inboxCheck(DB::table('core_jobs')->count() === 0, 'Queue trigger must wait for commit');
});
inboxCheck(DB::table('core_event_inbox')->count() === 1 && DB::table('core_jobs')->count() === 1, 'Inbox must be idempotent per listener/event and trigger after commit');
inboxRejects(fn () => $bus->publish(DomainEvent::create('producer.record.created', 2, 'record', '11', [])), 'Cross-company publish accepted');
$context->clear();
Artisan::call('queue:work', ['connection' => 'database', '--stop-when-empty' => true, '--tries' => 1]);
$row = DB::table('core_event_inbox')->first();
inboxCheck($row->status === 'processed' && $row->attempts === 1 && ConsumerHandler::$seenCompany === 1, 'Queued trigger must process with event company context');
inboxCheck($context->snapshot() === null && DB::table('consumer_effects')->count() === 1, 'Context must be restored after processing');
inboxCheck(app(InboxProcessor::class)->process($row->id) === 'skipped' && DB::table('consumer_effects')->count() === 1, 'Processed entries must not run twice');
// Retries with backoff, rollback of failed handler effects and dead letters.
$context->activate(1);
$second = $event();
$bus->publish($second);
DB::table('core_jobs')->delete();
$processor = new InboxProcessor(DB::connection(), $subscriptions, $context, 3, 0, 900);
$secondId = (int) DB::table('core_event_inbox')->where('event_id', $second->eventId)->value('id');
ConsumerHandler::$failuresLeft = 2;
inboxCheck($processor->process($secondId) === 'retrying' && $processor->process($secondId) === 'retrying', 'Transient failures must retry');
inboxCheck(DB::table('consumer_effects')->where('event_id', $second->eventId)->count() === 0, 'Failed handler effects must roll back');
inboxCheck(str_contains((string) DB::table('core_event_inbox')->where('id', $secondId)->value('last_error'), 'transient failure'), 'Last error must be recorded');
inboxCheck($processor->process($secondId) === 'processed' && $context->companyId() === 1, 'Third attempt must succeed and restore caller context');
$third = $event();
$bus->publish($third);
$thirdId = (int) DB::table('core_event_inbox')->where('event_id', $third->eventId)->value('id');
ConsumerHandler::$failuresLeft = 99;
foreach (['retrying', 'retrying', 'dead'] as $expected) {
    inboxCheck($processor->process($thirdId) === $expected, 'Expected '.$expected.' after repeated failure');
}
inboxCheck($processor->drain() === 0, 'Dead letters must not be drained');
inboxCheck(Artisan::call('erp:events:retry', ['id' => 999]) === 1 && Artisan::call('erp:events:retry', ['id' => $thirdId]) === 0, 'Retry command must only revive dead letters');
ConsumerHandler::$failuresLeft = 0;
inboxCheck($processor->drain() === 1 && DB::table('core_event_inbox')->where('id', $thirdId)->value('status') === 'processed', 'Revived dead letter must process');
$fourth = $event();
$bus->publish($fourth);
ConsumerHandler::$failuresLeft = 1;
$default = app(InboxProcessor::class);
$fourthId = (int) DB::table('core_event_inbox')->where('event_id', $fourth->eventId)->value('id');
inboxCheck($default->process($fourthId) === 'retrying' && $default->drain() === 0, 'Backoff must delay the next attempt');
inboxCheck(now()->addSeconds(25)->lt(DB::table('core_event_inbox')->where('id', $fourthId)->value('available_at')), 'Default backoff must be at least the base delay');
DB::table('core_event_inbox')->where('id', $fourthId)->update(['status' => 'processing', 'locked_at' => now()->subMinutes(20), 'available_at' => now()->subMinute()]);
inboxCheck($default->drain() === 1 && DB::table('core_event_inbox')->where('id', $fourthId)->value('status') === 'processed', 'Stale processing entries must be recovered');
DB::table('core_event_inbox')->insert(['listener' => 'ghost.listener', 'event_id' => 'x', 'event' => 'ghost.thing.done', 'company_id' => 1, 'envelope' => '{}', 'status' => 'pending', 'attempts' => 0, 'available_at' => now()->subMinute()]);
inboxCheck($default->drain() === 0 && $default->process((int) DB::table('core_event_inbox')->where('listener', 'ghost.listener')->value('id')) === 'skipped', 'Entries of unregistered listeners must remain pending');
DB::table('core_event_inbox')->where('id', $row->id)->update(['processed_at' => now()->subDays(40)]);
Artisan::call('erp:events:prune', ['--days' => 30]);
inboxCheck(DB::table('core_event_inbox')->where('id', $row->id)->doesntExist() && DB::table('core_event_inbox')->where('listener', 'ghost.listener')->exists(), 'Prune must delete only old processed entries');
$scheduled = array_map(fn ($event) => (string) $event->command, app(Schedule::class)->events());
foreach (['erp:events:drain', 'queue:work database', 'erp:events:prune'] as $command) {
    inboxCheck(count(array_filter($scheduled, fn ($line) => str_contains($line, $command))) === 1, 'Missing scheduled command: '.$command);
}
echo "Durable event inbox, retry and scheduler tests passed.\n";
