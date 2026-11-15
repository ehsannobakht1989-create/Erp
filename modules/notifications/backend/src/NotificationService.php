<?php
declare(strict_types=1);

namespace Modules\Notifications;

use Core\Contracts\NotifierContract;
use Core\Database\Connection;

final class NotificationService implements NotifierContract
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function notify(int $companyId, int $userId, string $channel, string $template, array $data = []): void
    {
        $this->db->execute(
            'INSERT INTO notifications__messages (company_id, user_id, channel, template, payload_json, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)',
            [$companyId, $userId, $channel, $template, (string) json_encode($data, JSON_UNESCAPED_UNICODE), 'sent', gmdate('Y-m-d H:i:s')]
        );
    }

    /** @return array<int, string> */
    public function supportedEvents(): array
    {
        return ['notifications.message.sent'];
    }
}
