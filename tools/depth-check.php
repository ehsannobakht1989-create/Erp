#!/usr/bin/env php
<?php

declare(strict_types=1);

require_once __DIR__.'/depth/PhpAnalyzer.php';
require_once __DIR__.'/depth/Inspector.php';

use Erp\Depth\Inspector;

function usage(): never
{
    fwrite(STDERR, "Usage: php tools/depth-check.php <module-id>|--all [--gate] [--json]\n");
    exit(2);
}

$args = array_slice($argv, 1);
$gate = false;
$json = false;
$selector = null;
foreach ($args as $arg) {
    if ($arg === '--gate' && ! $gate) {
        $gate = true;
    } elseif ($arg === '--json' && ! $json) {
        $json = true;
    } elseif ($selector === null && ($arg === '--all' || preg_match('/^[a-z][a-z0-9-]*$/D', $arg))) {
        $selector = $arg;
    } else {
        usage();
    }
}
if ($selector === null) {
    usage();
}

$root = dirname(__DIR__);
try {
    $inspector = new Inspector($root);
    $reports = $selector === '--all' ? $inspector->inspectAll() : [$inspector->inspect($selector)];
} catch (RuntimeException $e) {
    fwrite(STDERR, 'ERROR: '.$e->getMessage()."\n");
    exit(2);
}

if ($json) {
    echo json_encode(['implementation_complete' => false, 'modules' => $reports], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)."\n";
} else {
    foreach ($reports as $report) {
        echo $report['id'].' ('.$report['status'].'): UNVERIFIED — score unavailable'."\n";
        foreach ($report['missing'] as $gap) {
            echo '  - '.$gap."\n";
        }
        foreach ($report['violations'] as $violation) {
            echo '  - '.$violation."\n";
        }
    }
}

// No module can pass until every required category and test execution is implemented.
if ($gate) {
    foreach ($reports as $report) {
        if ($report['status'] === 'done') {
            exit(1);
        }
    }
}
