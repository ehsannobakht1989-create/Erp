<?php
declare(strict_types=1);

namespace Modules\DocOcr\Services;

use Core\Database\Connection;
use Modules\DocOcr\Contracts\DocOcrServiceInterface;

final class DocOcrService implements DocOcrServiceInterface
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function extract(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function result(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function approve(int $companyId, array $params = []): mixed
    {
        return [];
    }

    public function textOf(int $companyId, array $params = []): mixed
    {
        return [];
    }

    /** @return array<int, string> */
    public function supportedEvents(): array
    {
        return ['doc-ocr.extraction.completed'];
    }
}
