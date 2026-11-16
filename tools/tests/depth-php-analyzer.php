<?php

declare(strict_types=1);

require dirname(__DIR__).'/depth/PhpAnalyzer.php';

use Erp\Depth\PhpAnalyzer;

$source = <<<'PHP'
<?php
interface Contract { public function none(): void; }
class Example {
    public function emptyMethod(): void { /* none */ }
    public function emptyArray(): array { return []; }
    public function nullMethod() { return null; }
    public function zero(): int { return 0; }
    public function unavailable() { throw new \RuntimeException('bad'); }
    public function useful($a) { if ($a) { return 0; } return 1; }
    public function nonempty() { return [1]; }
    public function closure() { return function ($x): string { return "ok"; }; }
    public function interpolation($x) { echo "value {$x}"; return 1; }
    public function afterInterpolation() { return null; }
}
PHP;
$result = PhpAnalyzer::trivialFunctions($source);
$names = array_column($result, 'name');
if ($names !== ['emptyMethod', 'emptyArray', 'nullMethod', 'zero', 'unavailable', 'afterInterpolation']) {
    throw new RuntimeException('Unexpected trivial method findings: '.json_encode($result));
}
if ($result[0]['line'] !== 4 || $result[4]['reason'] !== 'only throws') {
    throw new RuntimeException('Incorrect method location or reason');
}
echo "Depth PHP analyzer tests passed.\n";
