# contractor-mgmt — Contractors & work statements (L3; requires party-master, contracts; optional hse, gate-visitor, ar-ap, project-mgmt, supplier-evaluation)

**Entities** Contractor{party_ref,categories,status[qualified|conditional|suspended],insurance_valid_until,hse_docs[],rating}; WorkPackage{contract_ref,name,scope,unit_prices|lump_sum,start,end,site_manager,status}; ProgressStatement(صورت‌وضعیت){wp,period,items[{ref,qty_this_period,cumulative}],gross,deductions[retention(حسن انجام کار),advance recovery,insurance,tax,penalty],net,status[draft|measured|reviewed|approved|paid]}; Advance{contract,amount,recovery_rule}; Retention ledger; ContractorWorker{contractor,name,id_ref(masked),training/doc validity}; PerformanceEvaluation{period,scores,grade}; Penalty/Claim.
**Rules** statement quantities cannot exceed WP quantities without change order; approval chain (site manager → engineer → finance); automatic deductions per contract terms; retention release rules; advance recovery proportional; payment request → ar-ap event; entry to site requires validity of insurance/HSE docs (consumed by gate-visitor); change orders & variations log.
**Events out** contractor-mgmt.statement.approved{→payment request}, contractor-mgmt.contractor.suspended. **In** contracts.contract.activated, ar-ap.payment.allocated, hse.permit.*.
**Service contract** ContractorService: isEligible(contractor,date), balance(contractor), retention(contractor).
**Permissions** contractor-mgmt.contractor|wp|statement|advance|evaluation.view|create|update|approve.
**Screens** contractor register, work packages with progress, statement entry/measurement sheet + print (صورت‌وضعیت), advance/retention ledgers, evaluations, eligibility checker.
**AI** anomaly (quantity inflation across periods), summarize contractor performance. **Datasets** statements, contractor_balances.
**Iran** صورت‌وضعیت موقت/قطعی, حسن انجام کار, بیمه پیمانکار, کسورات قانونی as configurable rules.
