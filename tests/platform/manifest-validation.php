<?php

declare(strict_types=1);

require dirname(__DIR__, 2).'/vendor/autoload.php';

use App\Support\Modules\ManifestValidator;
use App\Support\Validation\SchemaValidator;

function manifestCheck(bool $condition, string $message): void
{
    if (! $condition) {
        throw new RuntimeException($message);
    }
}
$validator = new SchemaValidator;
manifestCheck($validator->validate([], ['type' => 'object']) !== [], 'JSON arrays must not pass as objects');
manifestCheck($validator->validate((object) [], ['type' => 'array']) !== [], 'JSON objects must not pass as arrays');
manifestCheck($validator->validate('فا', ['type' => 'string', 'minLength' => 2]) === [], 'Length must count Unicode characters');
manifestCheck($validator->validate(['x', 'x'], ['type' => 'array', 'uniqueItems' => true]) !== [], 'Duplicates must fail');
manifestCheck($validator->validate(1.5, ['type' => 'integer']) !== [], 'Fraction must not pass integer');
try {
    $validator->validate('x', ['oneOf' => []]);
    throw new RuntimeException('Unknown schema keyword accepted');
} catch (LogicException $expected) {
    manifestCheck(str_contains($expected->getMessage(), 'Unsupported'), 'Wrong exception');
}
$root = dirname(__DIR__, 2);
$schema = $root.'/docs/module.schema.json';
$manifestValidator = new ManifestValidator;
manifestCheck($manifestValidator->validate($root.'/modules', $schema) === [], 'Repository manifests must validate');
$path = sys_get_temp_dir().'/erp-manifests-'.bin2hex(random_bytes(8));
mkdir($path.'/fixture', 0777, true);
try {
    $fixture = json_decode(file_get_contents($root.'/modules/oee/module.json'), true, 512, JSON_THROW_ON_ERROR);
    $fixture['id'] = 'fixture';
    $fixture['requires'] = $fixture['optional'] = $fixture['emits'] = $fixture['permissions'] = $fixture['menu'] = [];
    $write = function (array $data) use ($path): void {
        file_put_contents($path.'/fixture/module.json', json_encode($data, JSON_THROW_ON_ERROR));
    };
    $write($fixture);
    manifestCheck($manifestValidator->validate($path, $schema) === [], 'Valid fixture rejected');
    foreach ([['status', 'complete'], ['python', 'false'], ['datasets', (object) []], ['level', 7], ['permissions', ['fixture.bad-name.view']], ['emits', ['foreign.record.created']], ['requires', ['missing']], ['requires', ['fixture']]] as [$key, $value]) {
        $bad = $fixture;
        $bad[$key] = $value;
        $write($bad);
        manifestCheck($manifestValidator->validate($path, $schema) !== [], 'Invalid '.$key.' accepted');
    }
    $bad = $fixture;
    unset($bad['datasets']);
    $write($bad);
    manifestCheck($manifestValidator->validate($path, $schema) !== [], 'Missing required field accepted');
    file_put_contents($path.'/fixture/module.json', '{');
    manifestCheck($manifestValidator->validate($path, $schema) !== [], 'Malformed JSON accepted');
    echo "Manifest schema and naming tests passed.\n";
} finally {
    unlink($path.'/fixture/module.json');
    rmdir($path.'/fixture');
    rmdir($path);
}
