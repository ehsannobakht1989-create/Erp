# Core Kernel API

Modules plug into `Core\Kernel` without editing any central file.

## 1. Routes (`modules/<id>/backend/routes.php`)
Return a callable receiving `Core\Http\Router` and `Core\Container`. Routes are automatically prefixed with `/api/<id>`.

```php
<?php
declare(strict_types=1);

use Core\Container;
use Core\Http\Request;
use Core\Http\Response;
use Core\Http\Router;

return function (Router $router, Container $container): void {
    $router->get('/items', function (Request $req): Response {
        return Response::ok(['items' => []]);
    }, 'sample.item.view');
};
```

## 2. Event Listeners (`modules/<id>/backend/listeners.php`)
```php
<?php
declare(strict_types=1);

use Core\Container;
use Core\Event\EventBus;

return function (EventBus $bus, Container $container): void {
    $bus->listen('purchasing.po.received', function (array $envelope): void {
        // Handle event
    });
};
```

## 3. Services & Settings
- Bind interfaces in `Container` via `provides`/`consumes`.
- Read per-company settings via `Core\Support\SettingsService::get($companyId, $moduleId, $key)`.
- Use `Core\Database\Repository` for automatic `company_id` scoping, table-prefix guard, and `AuditContract` logging.
