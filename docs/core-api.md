# Laravel platform API for modules

Modules plug into the Laravel core without editing central files. The authoritative anatomy is `docs/MASTER-PLAN.md` §5; this page lists the hooks the core implements today.

## Provider (`modules/<id>/Providers/ModuleServiceProvider.php`)
Extend `App\Support\Modules\ModuleServiceProvider` (namespace `Modules\<Studly>\Providers`). Loaded only when the module is enabled (`config/erp-modules.php`, overridden by `core_modules` via `php artisan erp:modules:enable|disable`), in dependency order. `boot()` automatically loads:
- `database/migrations` (tables must use the `<id with _>__` prefix),
- `lang/` (namespaced and JSON translations),
- `routes/api.php`, mounted at `/api/<id>` with `api`, `auth:sanctum` and `company.context`; every route must also declare `can:<permission>` or boot fails.

Declarative hooks (override and return arrays; names must belong to the module):
- `listeners()`: `['name' => ['event' => 'other.entity.verb', 'handler' => Handler::class]]` — durable async inbox subscriptions; the event must be in manifest `listens`; handlers implement `App\Support\Events\DomainEventHandler` and must be idempotent.
- `datasets()`: `['<id>.dataset' => Provider::class]` — implement `App\Support\Datasets\DatasetProvider`; declared in manifest `datasets`.
- `aiTools()`: `['<id>.tool' => ['permission' => '<id>.x.view', 'handler' => Tool::class]]` — read-only `App\Support\Ai\AiTool`, declared in a manifest AI capability.

## Platform services (`app/Support/Contracts`, bound in the container)
Auth, company and permission resolution, data scope, audit, notifier, print and workflow are contracts whose defaults deny or report "unavailable" until the owning Phase 4 modules bind real implementations. Real core services: settings (`SettingsService`, manifest-declared keys only), numbering (`NumberingContract::next()` inside a transaction), domain events (`DomainEventBus::publish(DomainEvent::create(...))` after commit, inbox delivery), datasets (`DatasetContract::query()` with central company/permission/row-scope enforcement) and the AI gateway (`AiGatewayContract::invoke()`). Models use `App\Support\Tenancy\BelongsToCompany` and `App\Support\Audit\Auditable`.

## HTTP
Responses use `App\Support\Http\ApiResponse` (`{ok, data, error{code,message,details}, meta}`); exceptions map to Persian messages. Core endpoints: `POST /api/core/auth/login`, `GET /api/core/auth/me`, `POST /api/core/auth/logout`, `GET /api/core/meta` (loaded modules, granted abilities, filtered menu).

## Checks
`php tools/architecture-check.php` (isolation, prefixes, audit bypasses, raw SQL, route permissions), `php tools/depth-check.php <id>` (depth gate), `php artisan erp:modules:validate` (manifests and cycles).
