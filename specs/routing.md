# routing — Operations & work centers (L3; requires item-master; optional asset-registry, quality-inspection)

**Entities** WorkCenter{code,name,type[machine|line|manual|subcontract],unit_id,cost_center_id,calendar_id,capacity_per_shift,efficiency_pct,asset_ids[],labor_rate,machine_rate,overhead_rate}; Routing{item_id,code,revision,status,is_default,plant_id,effective_from/to}; Operation{seq,code,name,work_center_id,alt_work_centers[],setup_min,run_min_per_unit|per_batch,batch_size,labor_count,tooling_refs[],inspection_plan_ref,is_subcontract,supplier_id?,description,instructions_doc}; RoutingRevisionLog.
**Rules** unique op sequence; approved routing immutable (new revision); times in minutes, converted by calendar; alternate work centers with priority & efficiency factor; subcontract ops expose purchase service hook; capacity = calendar working minutes × efficiency.
**Events out** routing.routing.approved, workcenter.updated. **In** asset-registry.asset.status_changed (capacity availability, optional).
**Service contract** RoutingService: findActive(item,date,plant), workCenter(id), capacity(workCenter,from,to), standardTime(item,qty,opSeq?), costRates(workCenter,date).
**Permissions** routing.routing|workcenter.view|manage|approve.
**Screens** work center list/detail (capacity calendar), routing editor (ordered ops grid), process flow diagram view (simple), compare revisions, utilization placeholder widget (filled by scheduling).
**AI** autofill (op times from similar items, proposal), anomaly (times deviating from history via MES when enabled).
**Iran** support subcontract (برون‌سپاری) ops with supplier ref.
