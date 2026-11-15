<?php
declare(strict_types=1);

namespace Modules\FormBuilder;

use Core\Database\Connection;

final class FormBuilderService
{
    public function __construct(private readonly Connection $db)
    {
    }

    /** @param array<string, mixed> $schema @param array<string, mixed> $data @return array<string, string> */
    public function validateSubmission(array $schema, array $data): array
    {
        $errors = [];
        foreach ($schema['fields'] ?? [] as $field) {
            $key = (string) ($field['key'] ?? '');
            if (!empty($field['required']) && (!isset($data[$key]) || $data[$key] === '')) {
                $errors[$key] = 'فیلد الزامی است.';
            }
        }
        return $errors;
    }

    /** @param array<string, mixed> $data */
    public function submit(int $companyId, string $slug, int $version, array $data, ?int $userId = null): string
    {
        $now = gmdate('Y-m-d H:i:s');
        $this->db->execute(
            'INSERT INTO form_builder__instances (company_id, form_slug, form_version, data_json, created_by, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?)',
            [$companyId, $slug, $version, (string) json_encode($data, JSON_UNESCAPED_UNICODE), $userId, $now, $now]
        );
        return $this->db->lastInsertId();
    }

    /** @return array<int, string> */
    public function supportedEvents(): array
    {
        return ['form-builder.form.published', 'form-builder.submission.created'];
    }
}
