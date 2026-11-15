<?php
declare(strict_types=1);

namespace Modules\DocumentMgmt;

use Core\Database\Connection;

final class DocumentService
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function attach(int $companyId, string $entity, int|string $entityId, string $filename, string $mimeType, int $sizeBytes, string $storagePath): string
    {
        $now = gmdate('Y-m-d H:i:s');
        $this->db->execute(
            'INSERT INTO document_mgmt__documents (company_id, entity, entity_id, filename, mime_type, size_bytes, storage_path, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [$companyId, $entity, (string) $entityId, $filename, $mimeType, $sizeBytes, $storagePath, $now, $now]
        );
        return $this->db->lastInsertId();
    }

    /** @return array<int, string> */
    public function supportedEvents(): array
    {
        return ['document-mgmt.document.attached'];
    }
}
