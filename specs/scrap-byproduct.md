# scrap-byproduct — Scrap, by-products & recycling (L4; requires inventory, work-order; optional costing, sales, waste-environment)

**Entities** ScrapReason tree{code,name,category[process|material|machine|operator|design|other],controllable}; ScrapRecord{source[mes|wo|inventory|quality],item,qty,uom,reason,work_center,wo_ref,lot,cost_basis,recoverable_pct,disposition[dispose|recycle|resell|rework]}; ByProductRule{from_item,by_item,ratio,valuation_method}; RecycleLoop{scrap_item→raw_item,grade_map,loss_pct}; ScrapSale{buyer,weight,price,doc_ref} (links to sales as scrap-item invoices); ScrapTarget{item|work_center,rate_pct}.
**Rules** scrap records created from MES/WO reports and inventory adjustments tagged scrap; disposition decides stock movement (to scrap warehouse, regrind/recycle receipt as raw with grade, resale item); recycle loops preserve lot link; valuation hints sent to costing (material cost carried vs net realizable value); thresholds trigger alerts; metal-scrap yard weighing integrates with weighbridge when enabled.
**Events out** scrap-byproduct.scrap.recorded|disposed|recycled, byproduct.received. **In** mes.production.reported, work-order.output.received{kind:scrap|by_product}, quality-inspection.lot.decided{reject→scrap}.
**Service contract** ScrapService: rateByWorkCenter(range), rateByItem(range), expectedByProduct(item,qty), disposition(recordId).
**Permissions** scrap-byproduct.record.view|create|approve|dispose, scrap-byproduct.reason.manage, scrap-byproduct.rule.manage.
**Screens** scrap entry (mobile), Pareto by reason/work center/item, disposition worklist, recycle loop designer, scrap sales list, targets vs actual.
**AI** anomaly (scrap spikes by operator/shift), next_action (top controllable reasons), summarize.
**Iran** steel mill/foundry scrap grades, weight-based valuation (ton), scrap resale (ضایعات فروشی) workflow.
