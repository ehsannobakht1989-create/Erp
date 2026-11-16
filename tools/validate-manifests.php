<?php

declare(strict_types=1);

require_once dirname(__DIR__).'/app/Support/Validation/SchemaValidator.php';
require_once dirname(__DIR__).'/app/Support/Modules/ModuleCatalog.php';
require_once dirname(__DIR__).'/app/Support/Modules/ManifestValidator.php';

use App\Support\Modules\ManifestValidator;

$root = dirname(__DIR__);
try {
    $errors = (new ManifestValidator)->validate($root.'/modules', $root.'/docs/module.schema.json');
} catch (Throwable $e) {
    fwrite(STDERR, 'ERROR: '.$e->getMessage()."\n");
    exit(1);
}
foreach ($errors as $error) {
    fwrite(STDERR, 'ERROR: '.$error."\n");
}
if ($errors !== []) {
    exit(1);
}
echo "All module manifests are valid. Zero errors, zero cycles.\n";
