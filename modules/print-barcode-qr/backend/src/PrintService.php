<?php
declare(strict_types=1);

namespace Modules\PrintBarcodeQr;

use Core\Contracts\PrintContract;
use Core\Database\Connection;

final class PrintService implements PrintContract
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function render(int $companyId, string $template, array $data): string
    {
        $row = $this->db->fetchOne(
            'SELECT html_template FROM print_barcode_qr__templates WHERE company_id = ? AND code = ?',
            [$companyId, $template]
        );
        $html = $row['html_template'] ?? '<div>{{content}}</div>';
        foreach ($data as $k => $v) {
            if (is_scalar($v)) {
                $html = str_replace('{{' . $k . '}}', htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'), $html);
            }
        }
        return $html;
    }

    public function barcodeSvg(string $code): string
    {
        $escaped = htmlspecialchars($code, ENT_QUOTES, 'UTF-8');
        return "<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"160\" height=\"40\"><text x=\"10\" y=\"25\">$escaped</text></svg>";
    }
}
