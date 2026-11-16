<?php

declare(strict_types=1);

require dirname(__DIR__, 2).'/vendor/autoload.php';

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

$app = require dirname(__DIR__, 2).'/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$request = Request::create('/up', 'GET');
$response = $kernel->handle($request);
if ($response->getStatusCode() !== 200) {
    throw new RuntimeException('Laravel health endpoint did not return 200');
}
$kernel->terminate($request, $response);
echo 'Laravel '.$app->version()." health smoke test passed.\n";
