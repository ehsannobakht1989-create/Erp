# reorder-point — Reorder points, min/max & stock policy (L4; requires inventory; optional purchase-request, demand-forecast)

**Entities** StockPolicy{item,warehouse,method[min_max|rop|periodic_review|kanban],min,max,rop,safety_stock,eoq,review_days,service_level,lead_time_days,lead_time_sd,active}; ConsumptionStat{item,warehouse,period,avg_daily,sd}; Suggestion{item,warehouse,qty,reason,urgency,status[open|accepted|rejected|expired]}; AbcXyzClass{item,abc,xyz,period}; DeadStockCandidate.
**Rules** stats from inventory valuation feed (issues only, excluding transfers/adjustments); safety stock = z×σ formula options; ROP = demand during lead time + safety; suggestions created on schedule or when `inventory.stock.below_min`; accepted suggestion → purchase-request draft (source=reorder) or transfer proposal; ABC by annual consumption value, XYZ by variability; slow/dead stock by days-since-last-movement thresholds; policies approved by workflow when changed in bulk.
**Events out** reorder-point.suggestion.created|accepted, reorder-point.policy.changed, reorder-point.class.updated (item-master listens to set abc_class). **In** inventory.stock.below_min, inventory.issue.posted, purchase-order.po.received.
**Service contract** ReorderService: policy(item,warehouse), suggestions(filter), classOf(item), compute(itemFilter,dryRun).
**Permissions** reorder-point.policy.view|manage, reorder-point.suggestion.view|accept|reject, reorder-point.stats.view.
**Screens** policy grid (bulk edit/import), suggestions inbox (accept → PR), ABC/XYZ matrix, dead stock report, coverage-days dashboard, stockout risk list.
**AI** forecast (python: probabilistic lead-time demand, proposals for policy params), anomaly (consumption spikes), nl_report.
**Iran** longer lead-time variability for imported materials; override seasonal factors.
