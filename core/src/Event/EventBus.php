<?php
declare(strict_types=1);

namespace Core\Event;

use Core\Database\Connection;
use Throwable;

final class EventBus
{
    /** @var array<string, array<int, callable>> */
    private array $syncListeners = [];

    /** @var array<string, array<int, array{id: string, handler: callable}>> */
    private array $asyncListeners = [];

    public function __construct(private readonly ?Connection $db = null)
    {
    }

    public function listen(string $event, callable $handler, ?string $listenerId = null, bool $async = false): void
    {
        if ($async) {
            $this->asyncListeners[$event][] = [
                'id' => $listenerId ?? ('listener_' . count($this->asyncListeners[$event] ?? [])),
                'handler' => $handler,
            ];
            return;
        }
        $this->syncListeners[$event][] = $handler;
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    public function emit(
        string $event,
        array $payload = [],
        int $companyId = 0,
        int|string $entityId = '',
        ?int $actorId = null,
        ?string $correlationId = null,
        string $version = '1.0',
        bool $enqueueOutbox = true,
    ): array {
        $envelope = [
            'event' => $event,
            'version' => $version,
            'company_id' => $companyId,
            'entity_id' => $entityId,
            'payload' => $payload,
            'occurred_at' => gmdate('Y-m-d\TH:i:s\Z'),
            'actor_id' => $actorId,
            'correlation_id' => $correlationId ?? bin2hex(random_bytes(8)),
        ];

        // Sync listeners (emitting an event nobody listens to is fine and silent)
        foreach ($this->syncListeners[$event] ?? [] as $handler) {
            $handler($envelope);
        }

        // Transactional outbox for async handling
        if ($enqueueOutbox && $this->db !== null && !empty($this->asyncListeners[$event])) {
            $idemKey = hash('sha256', $event . '|' . $companyId . '|' . $entityId . '|' . $envelope['correlation_id']);
            $this->db->execute(
                'INSERT INTO core__outbox (event, version, company_id, entity_id, payload, actor_id, correlation_id, idempotency_key, status, attempts, next_attempt_at, created_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 0, ?, ?)',
                [
                    $event,
                    $version,
                    $companyId,
                    (string) $entityId,
                    (string) json_encode($payload, JSON_UNESCAPED_UNICODE),
                    $actorId,
                    $envelope['correlation_id'],
                    $idemKey,
                    'pending',
                    gmdate('Y-m-d H:i:s'),
                    gmdate('Y-m-d H:i:s'),
                ]
            );
        }

        return $envelope;
    }

    /**
     * Process pending outbox events (cron-friendly for cPanel).
     * @return array{processed: int, failed: int, dead_letter: int}
     */
    public function processOutbox(int $batchSize = 50, int $maxAttempts = 5): array
    {
        if ($this->db === null) {
            return ['processed' => 0, 'failed' => 0, 'dead_letter' => 0];
        }

        $now = gmdate('Y-m-d H:i:s');
        $rows = $this->db->fetchAll(
            'SELECT * FROM core__outbox WHERE status = ? AND next_attempt_at <= ? ORDER BY id ASC LIMIT ' . max(1, $batchSize),
            ['pending', $now]
        );

        $stats = ['processed' => 0, 'failed' => 0, 'dead_letter' => 0];

        foreach ($rows as $row) {
            $envelope = [
                'event' => $row['event'],
                'version' => $row['version'],
                'company_id' => (int) $row['company_id'],
                'entity_id' => $row['entity_id'],
                'payload' => json_decode((string) $row['payload'], true) ?: [],
                'occurred_at' => $row['created_at'],
                'actor_id' => $row['actor_id'] !== null ? (int) $row['actor_id'] : null,
                'correlation_id' => $row['correlation_id'],
            ];

            try {
                foreach ($this->asyncListeners[$row['event']] ?? [] as $listener) {
                    $listener['handler']($envelope);
                }
                $this->db->execute('UPDATE core__outbox SET status = ? WHERE id = ?', ['done', $row['id']]);
                $stats['processed']++;
            } catch (Throwable $e) {
                $attempts = (int) $row['attempts'] + 1;
                if ($attempts >= $maxAttempts) {
                    $this->db->execute(
                        'UPDATE core__outbox SET status = ?, attempts = ?, last_error = ? WHERE id = ?',
                        ['dead_letter', $attempts, $e->getMessage(), $row['id']]
                    );
                    $stats['dead_letter']++;
                } else {
                    $backoffSeconds = (int) pow(2, $attempts) * 5;
                    $nextAttempt = gmdate('Y-m-d H:i:s', time() + $backoffSeconds);
                    $this->db->execute(
                        'UPDATE core__outbox SET attempts = ?, next_attempt_at = ?, last_error = ? WHERE id = ?',
                        [$attempts, $nextAttempt, $e->getMessage(), $row['id']]
                    );
                    $stats['failed']++;
                }
            }
        }

        return $stats;
    }
}
