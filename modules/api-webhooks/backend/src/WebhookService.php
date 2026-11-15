<?php
declare(strict_types=1);

namespace Modules\ApiWebhooks;

use Core\Database\Connection;

final class WebhookService
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function signPayload(string $payload, string $secret): string
    {
        return hash_hmac('sha256', $payload, $secret);
    }

    public function createKey(int $companyId, string $name, array $scopes = []): string
    {
        $rawKey = 'erp_' . bin2hex(random_bytes(24));
        $this->db->execute(
            'INSERT INTO api_webhooks__keys (company_id, name, key_hash, scopes_json, created_at) VALUES (?, ?, ?, ?, ?)',
            [$companyId, $name, hash('sha256', $rawKey), (string) json_encode($scopes), gmdate('Y-m-d H:i:s')]
        );
        return $rawKey;
    }

    /** @return array<int, string> */
    public function supportedEvents(): array
    {
        return ['api-webhooks.webhook.dispatched'];
    }
}
