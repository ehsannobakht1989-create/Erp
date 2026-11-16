<?php

declare(strict_types=1);

use Builder\Resolver;

// Local builder UI only; never deploy this directory.
require_once dirname(__DIR__).'/src/Resolver.php';

header('Content-Type: application/json; charset=utf-8');
echo json_encode(['modules' => array_values((new Resolver(dirname(__DIR__, 2)))->manifests())], JSON_UNESCAPED_UNICODE);
