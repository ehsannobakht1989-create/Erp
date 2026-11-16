<?php

declare(strict_types=1);

namespace Tests\Platform;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;

final class ScaffoldTest extends TestCase
{
    public function test_application_boots_and_health_route_responds(): void
    {
        $app = require dirname(__DIR__, 2).'/bootstrap/app.php';
        self::assertInstanceOf(Application::class, $app);
        $kernel = $app->make(Kernel::class);
        $request = Request::create('/up', 'GET');
        $response = $kernel->handle($request);
        self::assertSame(200, $response->getStatusCode());
        $kernel->terminate($request, $response);
        restore_error_handler();
        restore_exception_handler();
    }
}
