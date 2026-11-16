<?php

declare(strict_types=1);

namespace App\Support\Events;

use App\Support\Modules\ModuleCatalog;
use InvalidArgumentException;

/** Durable async subscriptions registered by enabled module providers. */
final class EventSubscriptions
{
    /** @var array<string, array{event:string, handler:class-string<DomainEventHandler>}> */
    private array $listeners = [];

    public function __construct(private readonly ModuleCatalog $catalog) {}

    /** @param class-string<DomainEventHandler> $handler */
    public function async(string $event, string $listener, string $handler): void
    {
        $module = explode('.', $listener, 2)[0];
        if (preg_match('/^[a-z][a-z0-9-]*\.[a-z][a-z0-9_.-]{0,100}$/D', $listener) !== 1 || isset($this->listeners[$listener])) {
            throw new InvalidArgumentException('Listener must be a unique <module>.<name>.');
        }
        if (! in_array($event, $this->catalog->manifests()[$module]['listens'] ?? [], true)) {
            throw new InvalidArgumentException('Module '.$module.' must declare '.$event.' in manifest listens.');
        }
        if (! is_subclass_of($handler, DomainEventHandler::class)) {
            throw new InvalidArgumentException('Async handler must implement DomainEventHandler.');
        }
        $this->listeners[$listener] = ['event' => $event, 'handler' => $handler];
    }

    /** @return list<string> */
    public function listenersFor(string $event): array
    {
        return array_keys(array_filter($this->listeners, fn (array $listener) => $listener['event'] === $event));
    }

    /** @return class-string<DomainEventHandler>|null */
    public function handler(string $listener): ?string
    {
        return $this->listeners[$listener]['handler'] ?? null;
    }

    /** @return list<string> */
    public function listeners(): array
    {
        return array_keys($this->listeners);
    }
}
