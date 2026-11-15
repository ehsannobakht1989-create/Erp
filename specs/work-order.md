# work-order — دستور کار تولید (L4; requires: bom, routing, inventory)

**Purpose**: Plan-to-complete execution record for making items; the hub other production modules attach to.

**Entities**:
- `WorkOrder`: `number`, `item_id`, `qty_planned`, `qty_completed`, `qty_scrap`, `uom`, `type` (`make_to_stock|make_to_order|rework|project|batch`), `priority`, `plant_id`, `sales_ref?`, `parent_wo_id?`, `bom_id+revision` (snapshot), `routing_id+revision` (snapshot), `planned_start`, `planned_end`, `actual_start`, `actual_end`, `warehouse_in`, `warehouse_out`, `status` (`draft|planned|released|in_progress|completed|closed|cancelled|on_hold`)
- `WOComponent`: `item`, `qty_required`, `qty_issued`, `qty_returned`, `uom`, `issue_method` (`manual|backflush`), `warehouse`, `lot_policy`, `substitute_used`
- `WOOperation`: `seq`, `work_center_id`, `planned_setup/run`, `actual_setup/run`, `qty_in`, `qty_good`, `qty_scrap`, `status` (`pending|ready|running|paused|done|skipped`), `is_subcontract`
- `WOReceipt`: `kind` (`finished|by_product|scrap`), `item`, `qty`, `lot`, `warehouse`
- `WOShortage`, `WOEvent` log

**Workflow**: `draft -> planned` (reserve materials) `-> released` (print/dispatch) `-> in_progress` (first report) `-> completed -> closed` (variance calc locks); cancel only before consumption or with reversal; hold/resume.

**Rules**:
- BOM/routing snapshot at release; multi-level optional child order creation.
- Material issue via `InventoryService::postMovement` with idempotency; backflush on operation/order completion.
- Over-production tolerance %, under-completion close with reason; rework creates linked order.
- Lot/serial capture for tracked components & outputs (feeds `lot-traceability`).

**Events out**: `work-order.wo.created|released|started|completed|closed|cancelled`, `work-order.material.issued|returned`, `work-order.operation.completed`, `work-order.output.received`, `work-order.shortage.detected`.
**Events in**: `mrp.planned_order.firmed`, `sales.order.confirmed` (MTO, optional), `mes.operation.reported`, `quality-inspection.lot.decided`, `eco-plm.change.released`.

**Service contract (`WorkOrderServiceInterface`)**:
`find(id)`, `open(filter)`, `create(dto)`, `release(id)`, `reportOperation(dto, idempotencyKey)`, `issueMaterial(woId, lines)`, `receiveOutput(woId, lines)`, `shortages(woId)`, `wipQty(item)`, `openSupply(item, dateRange)`, `close(id)`.

**Permissions**: `work-order.wo.view|create|update|release|cancel|close|reopen`, `work-order.issue|receive|report.post`, `work-order.override.tolerance`.

**AI**: `autofill`, `anomaly`, `next_action`, `summarize` (default OFF).

**Iran**: دستور ساخت و برگه همراه, bilingual item names, weights in kg/ton conversions common (steel/chemical).
