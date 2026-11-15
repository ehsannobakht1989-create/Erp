<?php
declare(strict_types=1);

use Core\Autoloader;
use Core\Http\Request;
use Core\Kernel;

$root = dirname(__DIR__);
require_once $root . '/core/src/Autoloader.php';
(new Autoloader($root))->register();

$kernel = new Kernel($root);
$response = $kernel->handle(Request::fromGlobals());
$response->send();
