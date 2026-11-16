<?php

declare(strict_types=1);

require dirname(__DIR__, 2).'/vendor/autoload.php';

use App\Support\Events\DomainEvent;
use App\Support\Events\DomainEventBus;
use App\Support\Tenancy\CompanyContext;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

function eventCheck(bool $condition, string $message): void
{
    if (! $condition) {
        throw new RuntimeException($message);
    }
}
$app = require dirname(__DIR__, 2).'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();
config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
DB::purge('sqlite');
$context = app(CompanyContext::class);
$context->activate(1);
$bus = app(DomainEventBus::class);
$received = [];
Event::listen(DomainEvent::class, function (DomainEvent $event) use (&$received): void {
    $received[] = $event;
});
$event = DomainEvent::create('inventory.receipt.posted', 1, 'receipt', '7', ['quantity' => '1.0000'], 5, 'correlation-1');
$envelope = $event->toArray();
eventCheck($envelope['company_id'] === 1 && $envelope['event_id'] !== '' && str_ends_with($envelope['occurred_at'], 'Z'), 'Envelope must include ID, tenant and UTC time');
DB::beginTransaction();
$bus->publish($event);
eventCheck(count($received) === 0, 'Event must not dispatch before commit');
DB::rollBack();
eventCheck(count($received) === 0, 'Rollback must discard event');
DB::beginTransaction();
DB::beginTransaction();
$bus->publish($event);
DB::commit();
eventCheck(count($received) === 0, 'Nested commit must not dispatch before outer commit');
DB::commit();
eventCheck(count($received) === 1 && $received[0] === $event, 'Outermost commit must dispatch event');
try {
    $bus->publish(DomainEvent::create('inventory.receipt.posted', 2, 'receipt', '8', []));
    throw new RuntimeException('Cross-company event accepted');
} catch (LogicException $e) {
    eventCheck(str_contains($e->getMessage(), 'Cross-company'), 'Unexpected failure');
}
try {
    DomainEvent::create('bad name', 1, 'receipt', '8', []);
    throw new RuntimeException('Malformed event accepted');
} catch (InvalidArgumentException $expected) {
    eventCheck(str_contains($expected->getMessage(), 'Invalid'), 'Unexpected validation error');
}
echo "Domain event transaction tests passed.\n";
