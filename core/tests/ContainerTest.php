<?php
declare(strict_types=1);

use Core\Config;
use Core\Container;

return function (): void {
    $c = new Container();
    $cfg = new Config(['app' => ['name' => 'ERP']]);
    $c->singleton(Config::class, $cfg);

    $resolved = $c->make(Config::class);
    assertEquals('ERP', $resolved->string('app.name'));
    assertTrue($resolved === $c->make(Config::class), 'Singleton should return same instance');
};
