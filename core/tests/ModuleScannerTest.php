<?php
declare(strict_types=1);

use Core\Module\ModuleScanner;

return function (string $root): void {
    $scanner = new ModuleScanner($root);
    $sorted = $scanner->scan();
    assertTrue(count($sorted) >= 70, 'Should discover all module stubs');

    $keys = array_keys($sorted);
    $posItem = array_search('item-master', $keys, true);
    $posInv = array_search('inventory', $keys, true);
    assertTrue($posItem !== false && $posInv !== false && $posItem < $posInv, 'item-master must precede inventory');
};
