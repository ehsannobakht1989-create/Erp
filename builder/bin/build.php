#!/usr/bin/env php
<?php
declare(strict_types=1);

use Builder\Packager;
use Builder\ProfileParser;
use Builder\Resolver;
use Core\Autoloader;

$root = dirname(__DIR__, 2);
require_once $root . '/core/src/Autoloader.php';
(new Autoloader($root))->register();

spl_autoload_register(function (string $class) use ($root): void {
    if (str_starts_with($class, 'Builder\\')) {
        $file = $root . '/builder/src/' . str_replace('\\', '/', substr($class, 8)) . '.php';
        if (is_file($file)) {
            require_once $file;
        }
    }
});

$idx = array_search('--profile', $argv, true);
$profilePath = $idx !== false ? ($argv[$idx + 1] ?? '') : '';
if ($profilePath === '') {
    fwrite(STDERR, "Usage: php builder/bin/build.php --profile profiles/minimal.yaml\n");
    exit(1);
}

$profile = ProfileParser::parseFile($root . '/' . ltrim($profilePath, '/'));
$resolved = (new Resolver($root))->resolve($profile);
$result = (new Packager($root))->package($resolved);

echo "Built {$result['zip']} ({$result['file_count']} files, {$result['size_bytes']} bytes)\n";
