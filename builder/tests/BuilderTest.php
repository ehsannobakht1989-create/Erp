<?php
declare(strict_types=1);

use Builder\ProfileParser;
use Builder\Resolver;

return function (string $root): void {
    spl_autoload_register(function (string $class) use ($root): void {
        if (str_starts_with($class, 'Builder\\')) {
            $file = $root . '/builder/src/' . str_replace('\\', '/', substr($class, 8)) . '.php';
            if (is_file($file)) {
                require_once $file;
            }
        }
    });

    $profile = ProfileParser::parseFile($root . '/profiles/minimal.yaml');
    $resolved = (new Resolver($root))->resolve($profile);
    assertTrue(in_array('inventory', $resolved['modules'], true), 'minimal profile should include inventory');
    assertTrue(in_array('item-master', $resolved['modules'], true), 'minimal profile should include item-master');
};
