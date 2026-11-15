# purchase-request — Purchase requisition (L2; requires item-master, org-structure; optional budgeting, inventory)

**Entities** PR{number,requester_id,unit_id,cost_center_id,project_ref,needed_by,priority[normal|urgent|critical],justification,status,source[manual|mrp|reorder|maintenance]}; PRLine{item_id|free_text_desc,qty,uom,estimated_price,needed_by,suggested_supplier_id,notes,status[open|rfq|ordered|closed|cancelled],converted_qty}; PRTemplate.
**Workflow** draft→submitted→dept_approval→procurement_review→approved→(partially_converted|converted)→closed; reject/return; urgent skips levels by rule; approval chain from org-structure + amount thresholds (settings).
**Rules** free-text lines allowed only if setting on and require later item creation; budget check (BudgetService optional, warn|block); stock-on-hand shown at entry (InventoryService optional); consolidation of approved lines across PRs; auto-created PRs from events (mrp.requisition.suggested, reorder-point.suggested, cmms.workorder.parts_shortage) arrive as drafts with source tag.
**Events out** purchase-request.pr.submitted|approved|rejected, line.converted. **In** mrp.requisition.suggested, reorder-point.suggestion.accepted, rfq.awarded, purchase-order.line.created (update converted_qty).
**Service contract** PurchaseRequestService: openLines(filter), markConverted(lineId,qty,targetRef), release(lineId), find(id).
**Permissions** purchase-request.pr.view|create|update|submit|approve|consolidate|cancel.
**Screens** my requests, approvals inbox widget, PR form (with stock + last price hints), procurement workbench (select lines → create RFQ or PO), status board, aging report.
**AI** autofill (items/qty from free text or maintenance WO, proposal), anomaly (duplicate/excess requests), summarize.
**Iran** درخواست خرید print form with signatures section.
