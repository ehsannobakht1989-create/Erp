# Events & Service Contracts

Modules MUST NEVER query or join another module's database tables. All cross-module communication happens via **Events** or **Service Interfaces**.

## 1. Event Envelope
Every event published to the core event bus uses this JSON/array envelope:
```json
{
  "event": "inventory.receipt.posted",
  "version": "1.0",
  "company_id": 42,
  "entity_id": 1089,
  "payload": {},
  "occurred_at": "2026-11-15T10:30:00Z",
  "actor_id": 7,
  "correlation_id": "req-9f8e7d6c"
}
```
- **Naming**: `<module>.<entity>.<verb_past>` (e.g. `purchasing.po.received`, `work-order.job.completed`).
- **Backward compatibility**: New fields may be added to `payload`; existing fields must not be removed or change type within the same major `version`.

## 2. Standard Core Flow Events
- **Procure-to-Pay**: `purchase-order.po.received` -> `inventory.receipt.posted` -> `accounting-gl.journal.requested` -> `costing.cost.updated`
- **Plan-to-Produce**: `work-order.order.completed` -> `inventory.issue.posted` / `inventory.receipt.posted` -> `costing.cost.updated`
- **Order-to-Cash**: `sales.invoice.issued` -> `inventory.delivery.posted` -> `ar-ap.receivable.created` -> `accounting-gl.journal.requested`
- **Quality**: `quality-inspection.lot.rejected` -> `ncr-capa.ncr.opened`

## 3. Service Interfaces (`provides` / `consumes`)
- Shared PHP interfaces live in `core/contracts/<ServiceName>.php` or are declared in the providing module's `provides` array.
- Consuming modules depend ONLY on the interface, never on concrete classes.
- **Versioning**: Breaking interface changes require a new interface version (e.g. `InventoryServiceV2`) or a major module version bump.
- **Absent Optional Modules**: When a module declares a dependency in `optional` (not `requires`), the core DI container MUST return a **Null-Object** implementation (or `null` safely handled by the caller) if that module is not installed. Optional event listeners are simply not registered and skipped silently.

## 4. Generic Core Engines
Modules MUST reuse core engines by declaration rather than re-implementing them:
- **Workflow Engine**: Declare state machines and approval steps in `workflows` (`module.json`).
- **Dynamic Fields**: Register extensible custom fields per entity without altering core tables.
- **Form Builder**: Define declarative form schemas for UI rendering and validation.
- **Print Templates**: Register Jalali-aware HTML/PDF/barcode print layouts.
- **Notification Engine**: Emit domain events; notification rules dispatch SMS/email/in-app alerts.
- **Report Builder**: Register data sources and columns for filtering, sorting, and export.
