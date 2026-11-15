<?php
declare(strict_types=1);

use Core\Container;
use Core\Event\EventBus;
use Modules\ItemMaster\Services\ItemMasterService;

return function (EventBus $bus, Container $container): void {
    $bus->listen('inventory.stock.first_posted', function (array $env) use ($container): void {
        $itemId = (int) ($env['payload']['item_id'] ?? 0);
        if ($itemId > 0) {
            $container->make(ItemMasterService::class)->markUsed((int) $env['company_id'], $itemId, 'inventory');
        }
    });
};
