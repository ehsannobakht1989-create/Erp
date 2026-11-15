<?php
declare(strict_types=1);

namespace Core\Stubs;

use Core\Contracts\PrintContract;

final class NullPrint implements PrintContract
{
    public function render(int $companyId, string $template, array $data): string
    {
        return '';
    }
}
