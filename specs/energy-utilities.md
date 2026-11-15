# energy-utilities — Energy & utilities (L5; optional asset-registry, work-order, mes, costing, condition-monitoring-iot, python)

**Entities** Meter{type[electricity|gas|water|steam|compressed_air|fuel],location,asset_ref,multiplier,unit,parent_meter}; MeterReading{meter,ts,value,source[manual|iot|import]}; Tariff{type,from,to,bands[time_of_use,price],demand_charge,fixed_charge} (data); UtilityBill{type,period,consumption,amount,peak_demand,power_factor,file}; AllocationRule{meter|utility,cost_center|work_center|product driver}; EnergyTarget{scope,metric,target,period}; EmissionFactor{energy_type,factor,year} (data); SEC (specific energy consumption) computed per product/work center.
**Rules** consumption = reading deltas with rollover/replace handling; submeter hierarchy reconciliation (sum of children vs parent); tariff cost estimation by TOU; bill vs metered variance alert; allocation to cost centers/work centers by rules and to production via output from WorkOrder/MES (kWh/ton, m³/unit); overhead pool feed to costing as event aggregated per period; targets and alerts; carbon estimate via factors (data, user-maintained).
**Events out** energy-utilities.period.allocated{pool,amount} (for costing), energy-utilities.target.breached. **In** condition-monitoring-iot readings via MonitoringService (optional), work-order.output.received, mes.production.reported.
**Service contract** EnergyService: consumption(scope,range), cost(scope,range), sec(item|workCenter,range), allocation(period).
**Permissions** energy-utilities.meter|reading|tariff|bill|target.view|manage.
**Screens** meters tree, reading entry (mobile, scan QR), bill entry & reconciliation, consumption dashboard (daily/TOU/peak), SEC charts, allocation setup, targets.
**AI** anomaly/forecast consumption (python), next_action (shift load to cheaper tariff bands), summarize. **Datasets** consumption, bills, sec.
**Iran** برق/گاز/آب bill fields (peak/mid/low tariff bands as data), power factor penalty fields.
