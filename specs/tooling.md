# tooling — Tools, dies, molds & fixtures (L4; requires item-master; optional asset-registry, cmms, routing, calibration)

**Entities** Tool{code,name,type[die|mold|jig|fixture|cutting|gauge_holder],status[available|in_use|maintenance|quarantine|retired],location,owner_unit,life_unit[cycles|hours|strokes],life_limit,life_used,warning_pct,item_links[],work_center_links[],acquisition_cost,supplier}; ToolAssignment{tool,wo_operation,from,to}; ToolUsageLog{tool,qty,source}; ToolMaintenance{type[sharpen|repair|refurbish|inspect],cmms_wo_ref}; ToolMovement{from,to,by,at}.
**Rules** check-out requires status available; life accumulates from MES reports (qty good+scrap × cavities); warning/limit alerts block or warn per setting; end-of-life triggers maintenance proposal/cmms WO; compatibility enforced between tool, item, work center; cost-per-piece amortization feed for costing.
**Events out** tooling.tool.checked_out|returned|life_warning|life_exhausted. **In** mes.production.reported, cmms.workorder.completed, work-order.operation.completed.
**Service contract** ToolingService: availability(tool|item,range), lifeLeft(tool), required(woOperation), amortizationPerUnit(tool,item).
**Permissions** tooling.tool.view|manage, tooling.checkout|return, tooling.maintenance.manage.
**Screens** tool register, tool card with life gauge & history, check-out/in by scan, maintenance board, tools needed this week (from schedule), end-of-life list.
**AI** forecast (remaining life), anomaly (abnormal wear rate).
