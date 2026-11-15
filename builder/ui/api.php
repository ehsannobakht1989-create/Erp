<?php
declare(strict_types=1);

use Core\Autoloader;
use Core\Module\ModuleScanner;

$root = dirname(__DIR__, 2);
require_once $root . '/core/src/Autoloader.php';
(new Autoloader($root))->register();

header('Content-Type: application/json; charset=utf-8');
$scanner = new ModuleScanner($root);
echo json_encode(['modules' => array_values($scanner->scan())], JSON_UNESCAPED_UNICODE);
