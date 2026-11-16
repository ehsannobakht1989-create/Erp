#!/usr/bin/env php
<?php

declare(strict_types=1);

use Builder\Packager;
use Builder\ProfileParser;
use Builder\Resolver;

$root = dirname(__DIR__, 2);
require_once $root.'/builder/src/ProfileParser.php';
require_once $root.'/builder/src/Resolver.php';
require_once $root.'/builder/src/Packager.php';

$index = array_search('--profile', $argv, true);
$profilePath = $index !== false ? (string) ($argv[$index + 1] ?? '') : '';
if ($profilePath === '') {
    fwrite(STDERR, "Usage: php builder/bin/build.php --profile profiles/minimal.yaml\n");
    exit(1);
}
try {
    $resolved = (new Resolver($root))->resolve(ProfileParser::parseFile($root.'/'.ltrim($profilePath, '/')));
    $result = (new Packager($root))->package($resolved);
} catch (RuntimeException $e) {
    fwrite(STDERR, 'ERROR: '.$e->getMessage()."\n");
    exit(1);
}
echo "Built {$result['zip']} ({$result['file_count']} files, {$result['size_bytes']} bytes, vendor ".($result['vendor'] ? 'included' : 'MISSING').")\n";
