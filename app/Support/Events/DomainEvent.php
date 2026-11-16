<?php

declare(strict_types=1);

namespace App\Support\Events;

use DateTimeImmutable;
use DateTimeZone;
use Illuminate\Support\Str;
use InvalidArgumentException;

/** Immutable event envelope captured before the surrounding transaction commits. */
final readonly class DomainEvent
{
    /** @param array<string, mixed> $payload */
    public function __construct(
        public string $eventId,
        public string $event,
        public int $version,
        public int $companyId,
        public string $entityType,
        public string $entityId,
        public array $payload,
        public DateTimeImmutable $occurredAt,
        public ?int $actorId,
        public string $correlationId,
    ) {
        if ($eventId === '' || ! preg_match('/^[a-z][a-z0-9-]*\.[a-z][a-z0-9_]*\.[a-z][a-z0-9_]*$/D', $event) ||
            $version < 1 || $companyId < 1 || $entityType === '' || $entityId === '' ||
            ($actorId !== null && $actorId < 1) || $correlationId === '') {
            throw new InvalidArgumentException('Invalid domain event envelope.');
        }
    }

    /** @param array<string, mixed> $payload */
    public static function create(string $event, int $companyId, string $entityType, string $entityId, array $payload, ?int $actorId = null, ?string $correlationId = null): self
    {
        return new self(
            (string) Str::uuid(), $event, 1, $companyId, $entityType, $entityId, $payload,
            new DateTimeImmutable('now', new DateTimeZone('UTC')), $actorId, $correlationId ?? (string) Str::uuid(),
        );
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        foreach (['event_id', 'event', 'version', 'company_id', 'entity_type', 'entity_id', 'payload', 'occurred_at', 'actor_id', 'correlation_id'] as $field) {
            if (! array_key_exists($field, $data)) {
                throw new InvalidArgumentException('Event envelope field missing: '.$field);
            }
        }
        if (! is_array($data['payload']) || ! is_int($data['version']) || ! is_int($data['company_id']) || ($data['actor_id'] !== null && ! is_int($data['actor_id']))) {
            throw new InvalidArgumentException('Invalid stored event envelope.');
        }

        return new self((string) $data['event_id'], (string) $data['event'], $data['version'], $data['company_id'], (string) $data['entity_type'], (string) $data['entity_id'], $data['payload'], new DateTimeImmutable((string) $data['occurred_at']), $data['actor_id'], (string) $data['correlation_id']);
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'event_id' => $this->eventId,
            'event' => $this->event,
            'version' => $this->version,
            'company_id' => $this->companyId,
            'entity_type' => $this->entityType,
            'entity_id' => $this->entityId,
            'payload' => $this->payload,
            'occurred_at' => $this->occurredAt->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d\TH:i:s.u\Z'),
            'actor_id' => $this->actorId,
            'correlation_id' => $this->correlationId,
        ];
    }
}
