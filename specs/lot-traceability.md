# lot-traceability — Genealogy & recall (L4; requires inventory; optional work-order, sales, purchase-order, party-master)

**Entities** TraceNode{type[receipt|lot|work_order|serial|delivery|customer],ref}; TraceEdge{from,to,qty,relation[consumed_in|produced_by|shipped_to|received_from|transformed_to|split_from],at}; Recall{number,reason,scope(lot/date/supplier/item),severity,status[draft|active|closed],decision_log}; RecallAction{recall,type[quarantine|notify|retrieve|destroy|rework],target,status}; TraceReportRun.
**Rules** edges generated from events only (no polling): receipt posted (supplier lot→lot), WO material issue/outputs (consumed→produced lots), deliveries (lot→customer), transfers/splits; graph queries forward/backward with qty weighting and time bounds; recall scope computed from graph, affected on-hand lots auto-quarantined via inventory event; shipped-to customer list with documents; mock recall exercise mode; target time KPI for trace report.
**Events out** lot-traceability.recall.initiated|closed, lot-traceability.quarantine.requested. **In** inventory.receipt.posted|issue.posted|transfer.*, work-order.material.issued|output.received, sales.delivery.posted, batch-formulation.batch.*.
**Service contract** TraceService: backward(lot|serial,depth), forward(lot|serial,depth), recallScope(criteria), affectedCustomers(scope), where(lot) (stock + shipped).
**Permissions** lot-traceability.view|report, lot-traceability.recall.manage|execute.
**Screens** trace explorer (interactive graph/tree both directions), recall wizard with impact list & actions, mock recall results, trace report print.
**AI** assistant (explain a lot's journey in plain Persian), anomaly (orphan lots / broken chains), summarize recall status.
