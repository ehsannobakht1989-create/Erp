# Module Manifest (`module.json`)

Every module under `modules/<id>/` MUST contain a `module.json` file conforming to `docs/module.schema.json`.

## Example Manifest

```json
{
  "id": "inventory",
  "status": "stub",
  "name": {"fa": "انبار", "en": "Inventory"},
  "version": "1.0.0",
  "category": "supply",
  "level": 2,
  "requires": ["item-master"],
  "optional": ["accounting-gl"],
  "provides": ["InventoryService"],
  "consumes": ["ItemMasterService"],
  "emits": ["inventory.receipt.posted"],
  "listens": ["purchasing.po.received"],
  "permissions": ["inventory.receipt.create", "inventory.receipt.approve"],
  "menu": [
    {
      "id": "inv",
      "title": {"fa": "انبار"},
      "icon": "warehouse",
      "route": "/inventory",
      "permission": "inventory.receipt.view"
    }
  ],
  "entities": ["warehouse", "stock_movement", "receipt"],
  "workflows": ["receipt_approval"],
  "settings": [{"key": "allow_negative_stock", "type": "bool", "default": false}],
  "industry_tags": ["all"],
  "regional": ["ir"],
  "python": false,
  "ai": {
    "enabled_default": false,
    "capabilities": [
      {
        "id": "assistant",
        "provider": "avalai",
        "tools": ["inventory.search_stock", "inventory.get_movement"],
        "proposals": ["inventory.propose_transfer"],
        "redact": ["unit_cost"],
        "max_tokens_per_call": 2000
      }
    ]
  }
}
```

## Validation Rules
- `id`: kebab-case (`^[a-z][a-z0-9-]*$`), must match the directory name `modules/<id>/`.
- `status`: `"stub" | "in_progress" | "done"`.
- `version`: valid Semantic Versioning (`MAJOR.MINOR.PATCH`).
- `level`: integer `0..6` matching `docs/dependency-map.md`.
- `emits` / `listens`: `<module>.<entity>.<verb_past>` (e.g. `inventory.receipt.posted`).
- `permissions`: `<module>.<entity>.<action>` (e.g. `inventory.receipt.approve`).
- `ai.capabilities[].tools`: every tool name MUST be namespaced with `<module>.` (e.g. `inventory.search_stock`).

## Dependency Resolution
- **Hard dependencies (`requires`)**: Auto-selected by `builder/` when this module is included in a factory profile. Missing target IDs fail validation.
- **Soft dependencies (`optional`)**: Never auto-selected. Features depending on optional modules activate via events or null-object service fallbacks when present.
- **Cycle Detection**: The directed graph of `requires` (and combined `requires + optional` where checked) MUST be acyclic (DAG). `tools/validate-manifests.php` rejects any cycle.

## Semver
- `MAJOR`: breaking changes to `provides` service interfaces, `emits` event payloads, or DB schema incompatibilities.
- `MINOR`: backward-compatible new features, events, or optional fields.
- `PATCH`: bug fixes and internal refactors.
