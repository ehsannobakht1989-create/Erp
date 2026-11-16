<?php

declare(strict_types=1);

require_once dirname(__DIR__).'/depth/PhpAnalyzer.php';
require_once dirname(__DIR__).'/depth/Inspector.php';

use Erp\Depth\Inspector;

function verify(bool $condition, string $message): void
{
    if (! $condition) {
        throw new RuntimeException($message);
    }
}

$root = sys_get_temp_dir().'/erp-depth-'.bin2hex(random_bytes(8));
mkdir($root.'/modules/example/src', 0777, true);
mkdir($root.'/specs', 0777, true);
try {
    file_put_contents($root.'/modules/example/module.json', json_encode(['id' => 'example', 'status' => 'partial'], JSON_THROW_ON_ERROR));
    file_put_contents($root.'/modules/example/src/a.php', "<?php\n// TODO: improve\n// not implemented\n");
    file_put_contents($root.'/modules/example/README.md', 'placeholder: documentation is not code');
    $inspector = new Inspector($root);
    $report = $inspector->inspect('example');
    verify($report['score'] === null && $report['pass'] === false, 'Incomplete inspector must not report a score or pass');
    verify(count($report['violations']) === 2, 'Two code markers should be detected; README should not be scanned');
    verify(str_contains($report['violations'][0], 'src/a.php:'), 'Marker location should be reported');
    verify(str_contains($report['missing'][0], 'Specification missing'), 'Missing specification must be reported');
    verify(count($inspector->inspectAll()) === 1, 'All selector should enumerate manifests');
    file_put_contents($root.'/specs/example.md', '# Example');
    verify(! str_contains($inspector->inspect('example')['missing'][0], 'Specification missing'), 'Existing specification should not be reported missing');
    foreach (['../example', 'not-found'] as $id) {
        try {
            $inspector->inspect($id);
            throw new LogicException('invalid module accepted');
        } catch (RuntimeException $expected) {
            verify(! $expected instanceof LogicException, 'invalid module must throw');
        }
    }
    file_put_contents($root.'/modules/example/module.json', '{');
    try {
        $inspector->inspect('example');
        throw new LogicException('invalid manifest accepted');
    } catch (RuntimeException $expected) {
        verify(! $expected instanceof LogicException, 'invalid manifest must throw');
    }
    echo "Depth inspector tests passed.\n";
} finally {
    unlink($root.'/modules/example/src/a.php');
    unlink($root.'/modules/example/README.md');
    unlink($root.'/modules/example/module.json');
    unlink($root.'/specs/example.md');
    rmdir($root.'/modules/example/src');
    rmdir($root.'/modules/example');
    rmdir($root.'/modules');
    rmdir($root.'/specs');
    rmdir($root);
}
