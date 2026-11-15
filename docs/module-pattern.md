# Module Pattern (`modules/<id>/`)

Reference implementation: `modules/item-master/`.

## Directory Layout (<= 50 files per module)
```
modules/<id>/
  module.json
  README.md                   # <= 10 lines
  openapi.yaml
  backend/
    migrations/001_init.sql   # <id>__<table>, company_id, created_at, updated_at
    routes.php                # returns fn(Router $r, Container $c)
    listeners.php             # returns fn(EventBus $b, Container $c)
    src/
      Contracts/              # <Module>ServiceInterface.php
      Repositories/           # extends Core\Database\Repository
      Services/               # business rules, transactions, events
      Controllers/            # thin HTTP adapters
    tests/                    # *Test.php discovered by tools/test.php
  web/
    routes.ts                 # default export: RouteObject[]
    widgets.ts                # dashboard widget definitions
    i18n/fa.json              # Persian UI strings
    types/index.ts            # TypeScript interfaces
    mocks/data.ts             # realistic Persian mock data
    api/hooks.ts              # resource hooks using @erp/hooks
    pages/                    # built with @erp/ui
```

## Conventions
- **Layering**: `Controller` -> `Service` -> `Repository`. Never put SQL or business rules in `routes.php`.
- **Error codes**: `VALIDATION_ERROR` (422), `NOT_FOUND` (404), `CONFLICT` (409), `FORBIDDEN` (403).
- **Events**: Emit `<id>.<entity>.<verb_past>` after DB commit; never query another module's tables.
- **Tests**: Test business rules, permissions, and AI `provider=off` via `php tools/test.php`.
