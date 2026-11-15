# LAUNCH SHEET — every module session prompt, ready to paste

Rules: ONE Arena session per module, own branch `module/<id>`, own PR. Prerequisite: PR #1 (infrastructure) merged and the combined spec PR (07b+09) merged. Open as many parallel sessions as Arena allows; merge PRs when CI is green (budget + manifests + tests). Order = group order; inside a group any order. Total module sessions: 69.


## GROUP A — Build first. Parallel OK. Merge before B. (8 modules)

### [A-01] party-master
```
Branch: module/party-master
Read tools/module-prompt-template.md and execute it exactly with ID=party-master.
Source of truth: specs/party-master.md. Also read Contracts of every module named in its requires/optional/consumes.
Integrate with other modules ONLY via events and Contracts; guard optional providers (nullable); never touch other modules' folders.
Open ONE PR from branch module/party-master and stop; do not start other modules.
```

### [A-02] org-structure
```
Branch: module/org-structure
Read tools/module-prompt-template.md and execute it exactly with ID=org-structure.
Source of truth: specs/org-structure.md. Also read Contracts of every module named in its requires/optional/consumes.
Integrate with other modules ONLY via events and Contracts; guard optional providers (nullable); never touch other modules' folders.
Open ONE PR from branch module/org-structure and stop; do not start other modules.
```

### [A-03] inventory
```
Branch: module/inventory
Read tools/module-prompt-template.md and execute it exactly with ID=inventory.
Source of truth: specs/inventory.md. Also read Contracts of every module named in its requires/optional/consumes.
Integrate with other modules ONLY via events and Contracts; guard optional providers (nullable); never touch other modules' folders.
FOCUS: posting atomicity and ledger immutability, row-lock concurrency tests (parallel posts), negative-stock setting, lot/serial/expiry, reversal docs, Kardex report correctness with a fixed test dataset.
Open ONE PR from branch module/inventory and stop; do not start other modules.
```

### [A-04] pricing
```
Branch: module/pricing
Read tools/module-prompt-template.md and execute it exactly with ID=pricing.
Source of truth: specs/pricing.md. Also read Contracts of every module named in its requires/optional/consumes.
Integrate with other modules ONLY via events and Contracts; guard optional providers (nullable); never touch other modules' folders.
Open ONE PR from branch module/pricing and stop; do not start other modules.
```

### [A-05] asset-registry
```
Branch: module/asset-registry
Read tools/module-prompt-template.md and execute it exactly with ID=asset-registry.
Source of truth: specs/asset-registry.md. Also read Contracts of every module named in its requires/optional/consumes.
Integrate with other modules ONLY via events and Contracts; guard optional providers (nullable); never touch other modules' folders.
Open ONE PR from branch module/asset-registry and stop; do not start other modules.
```

### [A-06] hr-core
```
Branch: module/hr-core
Read tools/module-prompt-template.md and execute it exactly with ID=hr-core.
Source of truth: specs/hr-core.md. Also read Contracts of every module named in its requires/optional/consumes.
Integrate with other modules ONLY via events and Contracts; guard optional providers (nullable); never touch other modules' folders.
Open ONE PR from branch module/hr-core and stop; do not start other modules.
```

### [A-07] accounting-gl
```
Branch: module/accounting-gl
Read tools/module-prompt-template.md and execute it exactly with ID=accounting-gl.
Source of truth: specs/accounting-gl.md. Also read Contracts of every module named in its requires/optional/consumes.
Integrate with other modules ONLY via events and Contracts; guard optional providers (nullable); never touch other modules' folders.
FOCUS: balanced-journal invariant everywhere, Jalali fiscal year/periods, posting-rule engine with the default rule pack, JournalRequest inbox/fix queue idempotency, trial balance + general ledger correctness tests with a hand-computed dataset.
Open ONE PR from branch module/accounting-gl and stop; do not start other modules.
```

### [A-08] bom
```
Branch: module/bom
Read tools/module-prompt-template.md and execute it exactly with ID=bom.
Source of truth: specs/bom.md. Also read Contracts of every module named in its requires/optional/consumes.
Integrate with other modules ONLY via events and Contracts; guard optional providers (nullable); never touch other modules' folders.
FOCUS: cycle detection, multi-level explosion with scrap/yield/phantoms/substitutes, safe formula evaluator, revision immutability, performance on 10-level deep BOMs.
Open ONE PR from branch module/bom and stop; do not start other modules.
```


## GROUP B — After A is merged. Parallel OK. (12 modules)

### [B-01] purchase-request
```
Branch: module/purchase-request
Read tools/module-prompt-template.md and execute it exactly with ID=purchase-request.
Source of truth: specs/purchase-request.md. Also read Contracts of every module named in its requires/optional/consumes.
Integrate with other modules ONLY via events and Contracts; guard optional providers (nullable); never touch other modules' folders.
Open ONE PR from branch module/purchase-request and stop; do not start other modules.
```

### [B-02] rfq
```
Branch: module/rfq
Read tools/module-prompt-template.md and execute it exactly with ID=rfq.
Source of truth: specs/rfq.md. Also read Contracts of every module named in its requires/optional/consumes.
Integrate with other modules ONLY via events and Contracts; guard optional providers (nullable); never touch other modules' folders.
Open ONE PR from branch module/rfq and stop; do not start other modules.
```

### [B-03] purchase-order
```
Branch: module/purchase-order
Read tools/module-prompt-template.md and execute it exactly with ID=purchase-order.
Source of truth: specs/purchase-order.md. Also read Contracts of every module named in its requires/optional/consumes.
Integrate with other modules ONLY via events and Contracts; guard optional providers (nullable); never touch other modules' folders.
FOCUS: approval thresholds by amount, revisions requiring re-approval, receipt tolerance, blanket orders, landed-cost charges exposure.
Open ONE PR from branch module/purchase-order and stop; do not start other modules.
```

### [B-04] sales
```
Branch: module/sales
Read tools/module-prompt-template.md and execute it exactly with ID=sales.
Source of truth: specs/sales.md. Also read Contracts of every module named in its requires/optional/consumes.
Integrate with other modules ONLY via events and Contracts; guard optional providers (nullable); never touch other modules' folders.
FOCUS: credit check via ArApService (optional), reservation on confirm, partial delivery/invoice flows, invoice fields complete for Moadian (stored, not submitted here).
Open ONE PR from branch module/sales and stop; do not start other modules.
```

### [B-05] routing
```
Branch: module/routing
Read tools/module-prompt-template.md and execute it exactly with ID=routing.
Source of truth: specs/routing.md. Also read Contracts of every module named in its requires/optional/consumes.
Integrate with other modules ONLY via events and Contracts; guard optional providers (nullable); never touch other modules' folders.
Open ONE PR from branch module/routing and stop; do not start other modules.
```

### [B-06] ar-ap
```
Branch: module/ar-ap
Read tools/module-prompt-template.md and execute it exactly with ID=ar-ap.
Source of truth: specs/ar-ap.md. Also read Contracts of every module named in its requires/optional/consumes.
Integrate with other modules ONLY via events and Contracts; guard optional providers (nullable); never touch other modules' folders.
FOCUS: 3-way match with tolerances and exception queue, allocation, aging, duplicate-invoice guard, posting via JournalRequest only.
Open ONE PR from branch module/ar-ap and stop; do not start other modules.
```

### [B-07] treasury
```
Branch: module/treasury
Read tools/module-prompt-template.md and execute it exactly with ID=treasury.
Source of truth: specs/treasury.md. Also read Contracts of every module named in its requires/optional/consumes.
Integrate with other modules ONLY via events and Contracts; guard optional providers (nullable); never touch other modules' folders.
FOCUS: complete cheque lifecycle state machine (received/issued/endorsed/bounced), Sayad id validation, reconciliation workbench.
Open ONE PR from branch module/treasury and stop; do not start other modules.
```

### [B-08] fixed-assets
```
Branch: module/fixed-assets
Read tools/module-prompt-template.md and execute it exactly with ID=fixed-assets.
Source of truth: specs/fixed-assets.md. Also read Contracts of every module named in its requires/optional/consumes.
Integrate with other modules ONLY via events and Contracts; guard optional providers (nullable); never touch other modules' folders.
Open ONE PR from branch module/fixed-assets and stop; do not start other modules.
```

### [B-09] attendance
```
Branch: module/attendance
Read tools/module-prompt-template.md and execute it exactly with ID=attendance.
Source of truth: specs/attendance.md. Also read Contracts of every module named in its requires/optional/consumes.
Integrate with other modules ONLY via events and Contracts; guard optional providers (nullable); never touch other modules' folders.
Open ONE PR from branch module/attendance and stop; do not start other modules.
```

### [B-10] payroll
```
Branch: module/payroll
Read tools/module-prompt-template.md and execute it exactly with ID=payroll.
Source of truth: specs/payroll.md. Also read Contracts of every module named in its requires/optional/consumes.
Integrate with other modules ONLY via events and Contracts; guard optional providers (nullable); never touch other modules' folders.
FOCUS: tax/insurance tables are DATA with sample tables for tests only (never hard-code real statutory rates), proration, loan installments, immutable payslip snapshots, `redact: *` for AI. Tests with hand-computed payslips.
Open ONE PR from branch module/payroll and stop; do not start other modules.
```

### [B-11] cmms
```
Branch: module/cmms
Read tools/module-prompt-template.md and execute it exactly with ID=cmms.
Source of truth: specs/cmms.md. Also read Contracts of every module named in its requires/optional/consumes.
Integrate with other modules ONLY via events and Contracts; guard optional providers (nullable); never touch other modules' folders.
Open ONE PR from branch module/cmms and stop; do not start other modules.
```

### [B-12] quality-inspection
```
Branch: module/quality-inspection
Read tools/module-prompt-template.md and execute it exactly with ID=quality-inspection.
Source of truth: specs/quality-inspection.md. Also read Contracts of every module named in its requires/optional/consumes.
Integrate with other modules ONLY via events and Contracts; guard optional providers (nullable); never touch other modules' folders.
Open ONE PR from branch module/quality-inspection and stop; do not start other modules.
```


## GROUP C — After A+B. Parallel OK. (9 modules)

### [C-01] work-order
```
Branch: module/work-order
Read tools/module-prompt-template.md and execute it exactly with ID=work-order.
Source of truth: specs/work-order.md. Also read Contracts of every module named in its requires/optional/consumes.
Integrate with other modules ONLY via events and Contracts; guard optional providers (nullable); never touch other modules' folders.
Open ONE PR from branch module/work-order and stop; do not start other modules.
```

### [C-02] mps
```
Branch: module/mps
Read tools/module-prompt-template.md and execute it exactly with ID=mps.
Source of truth: specs/mps.md. Also read Contracts of every module named in its requires/optional/consumes.
Integrate with other modules ONLY via events and Contracts; guard optional providers (nullable); never touch other modules' folders.
FOCUS: time fences, ATP calc, rough-cut capacity, scenarios.
Open ONE PR from branch module/mps and stop; do not start other modules.
```

### [C-03] mrp
```
Branch: module/mrp
Read tools/module-prompt-template.md and execute it exactly with ID=mrp.
Source of truth: specs/mrp.md. Also read Contracts of every module named in its requires/optional/consumes.
Integrate with other modules ONLY via events and Contracts; guard optional providers (nullable); never touch other modules' folders.
FOCUS: chunked/resumable runs (outbox slices), low-level codes, pegging, lot sizing rules, test with a 500-item synthetic multi-level BOM, performance numbers in PR notes.
Open ONE PR from branch module/mrp and stop; do not start other modules.
```

### [C-04] reorder-point
```
Branch: module/reorder-point
Read tools/module-prompt-template.md and execute it exactly with ID=reorder-point.
Source of truth: specs/reorder-point.md. Also read Contracts of every module named in its requires/optional/consumes.
Integrate with other modules ONLY via events and Contracts; guard optional providers (nullable); never touch other modules' folders.
Open ONE PR from branch module/reorder-point and stop; do not start other modules.
```

### [C-05] eco-plm
```
Branch: module/eco-plm
Read tools/module-prompt-template.md and execute it exactly with ID=eco-plm.
Source of truth: specs/eco-plm.md. Also read Contracts of every module named in its requires/optional/consumes.
Integrate with other modules ONLY via events and Contracts; guard optional providers (nullable); never touch other modules' folders.
Open ONE PR from branch module/eco-plm and stop; do not start other modules.
```

### [C-06] tooling
```
Branch: module/tooling
Read tools/module-prompt-template.md and execute it exactly with ID=tooling.
Source of truth: specs/tooling.md. Also read Contracts of every module named in its requires/optional/consumes.
Integrate with other modules ONLY via events and Contracts; guard optional providers (nullable); never touch other modules' folders.
Open ONE PR from branch module/tooling and stop; do not start other modules.
```

### [C-07] scrap-byproduct
```
Branch: module/scrap-byproduct
Read tools/module-prompt-template.md and execute it exactly with ID=scrap-byproduct.
Source of truth: specs/scrap-byproduct.md. Also read Contracts of every module named in its requires/optional/consumes.
Integrate with other modules ONLY via events and Contracts; guard optional providers (nullable); never touch other modules' folders.
Open ONE PR from branch module/scrap-byproduct and stop; do not start other modules.
```

### [C-08] batch-formulation
```
Branch: module/batch-formulation
Read tools/module-prompt-template.md and execute it exactly with ID=batch-formulation.
Source of truth: specs/batch-formulation.md. Also read Contracts of every module named in its requires/optional/consumes.
Integrate with other modules ONLY via events and Contracts; guard optional providers (nullable); never touch other modules' folders.
Open ONE PR from branch module/batch-formulation and stop; do not start other modules.
```

### [C-09] calibration
```
Branch: module/calibration
Read tools/module-prompt-template.md and execute it exactly with ID=calibration.
Source of truth: specs/calibration.md. Also read Contracts of every module named in its requires/optional/consumes.
Integrate with other modules ONLY via events and Contracts; guard optional providers (nullable); never touch other modules' folders.
Open ONE PR from branch module/calibration and stop; do not start other modules.
```


## GROUP D — After C. Parallel OK. (7 modules)

### [D-01] scheduling
```
Branch: module/scheduling
Read tools/module-prompt-template.md and execute it exactly with ID=scheduling.
Source of truth: specs/scheduling.md. Also read Contracts of every module named in its requires/optional/consumes.
Integrate with other modules ONLY via events and Contracts; guard optional providers (nullable); never touch other modules' folders.
FOCUS: finite-capacity heuristic in PHP (EDD/SPT/critical ratio + setup matrix), Gantt UI, conflict detection; optimizer hook is a separate module (schedule-optimizer).
Open ONE PR from branch module/scheduling and stop; do not start other modules.
```

### [D-02] mes
```
Branch: module/mes
Read tools/module-prompt-template.md and execute it exactly with ID=mes.
Source of truth: specs/mes.md. Also read Contracts of every module named in its requires/optional/consumes.
Integrate with other modules ONLY via events and Contracts; guard optional providers (nullable); never touch other modules' folders.
FOCUS: operator touch terminal UX (large buttons, Persian, scan-first), idempotent reports, offline-tolerant queue, machine-tag ingestion endpoint.
Open ONE PR from branch module/mes and stop; do not start other modules.
```

### [D-03] oee-downtime
```
Branch: module/oee-downtime
Read tools/module-prompt-template.md and execute it exactly with ID=oee-downtime.
Source of truth: specs/oee-downtime.md. Also read Contracts of every module named in its requires/optional/consumes.
Integrate with other modules ONLY via events and Contracts; guard optional providers (nullable); never touch other modules' folders.
Open ONE PR from branch module/oee-downtime and stop; do not start other modules.
```

### [D-04] costing
```
Branch: module/costing
Read tools/module-prompt-template.md and execute it exactly with ID=costing.
Source of truth: specs/costing.md. Also read Contracts of every module named in its requires/optional/consumes.
Integrate with other modules ONLY via events and Contracts; guard optional providers (nullable); never touch other modules' folders.
FOCUS: include a hand-calculated verification scenario in tests (purchase -> issue -> production -> completion -> variances -> journals), idempotent restartable cost run, landed-cost allocation, moving-average re-valuation on backdated receipts.
Open ONE PR from branch module/costing and stop; do not start other modules.
```

### [D-05] lot-traceability
```
Branch: module/lot-traceability
Read tools/module-prompt-template.md and execute it exactly with ID=lot-traceability.
Source of truth: specs/lot-traceability.md. Also read Contracts of every module named in its requires/optional/consumes.
Integrate with other modules ONLY via events and Contracts; guard optional providers (nullable); never touch other modules' folders.
Open ONE PR from branch module/lot-traceability and stop; do not start other modules.
```

### [D-06] ncr-capa
```
Branch: module/ncr-capa
Read tools/module-prompt-template.md and execute it exactly with ID=ncr-capa.
Source of truth: specs/ncr-capa.md. Also read Contracts of every module named in its requires/optional/consumes.
Integrate with other modules ONLY via events and Contracts; guard optional providers (nullable); never touch other modules' folders.
Open ONE PR from branch module/ncr-capa and stop; do not start other modules.
```

### [D-07] lims
```
Branch: module/lims
Read tools/module-prompt-template.md and execute it exactly with ID=lims.
Source of truth: specs/lims.md. Also read Contracts of every module named in its requires/optional/consumes.
Integrate with other modules ONLY via events and Contracts; guard optional providers (nullable); never touch other modules' folders.
Open ONE PR from branch module/lims and stop; do not start other modules.
```


## GROUP E — Can start together with C/D (only needs A+B merged). report-bi first if possible. (17 modules)

### [E-01] report-bi
```
Branch: module/report-bi
Read tools/module-prompt-template.md and execute it exactly with ID=report-bi.
Source of truth: specs/report-bi.md. Also read Contracts of every module named in its requires/optional/consumes.
Integrate with other modules ONLY via events and Contracts; guard optional providers (nullable); never touch other modules' folders.
FOCUS: DatasetProvider contract, permission/row-scope enforcement, row caps/timeouts, snapshots for heavy reports, scheduled exports. Implement dataset providers' registry so every module's manifest `datasets` appears in the catalog.
Open ONE PR from branch module/report-bi and stop; do not start other modules.
```

### [E-02] tax-moadian
```
Branch: module/tax-moadian
Read tools/module-prompt-template.md and execute it exactly with ID=tax-moadian.
Source of truth: specs/tax-moadian.md. Also read Contracts of every module named in its requires/optional/consumes.
Integrate with other modules ONLY via events and Contracts; guard optional providers (nullable); never touch other modules' folders.
FOCUS: follow ONLY official documents in docs/external/moadian/; if absent build behind interfaces with a MOCK adapter and write docs/external/moadian/TODO.md. Never guess crypto/signature/protocol details.
Open ONE PR from branch module/tax-moadian and stop; do not start other modules.
```

### [E-03] budgeting
```
Branch: module/budgeting
Read tools/module-prompt-template.md and execute it exactly with ID=budgeting.
Source of truth: specs/budgeting.md. Also read Contracts of every module named in its requires/optional/consumes.
Integrate with other modules ONLY via events and Contracts; guard optional providers (nullable); never touch other modules' folders.
Open ONE PR from branch module/budgeting and stop; do not start other modules.
```

### [E-04] contracts
```
Branch: module/contracts
Read tools/module-prompt-template.md and execute it exactly with ID=contracts.
Source of truth: specs/contracts.md. Also read Contracts of every module named in its requires/optional/consumes.
Integrate with other modules ONLY via events and Contracts; guard optional providers (nullable); never touch other modules' folders.
Open ONE PR from branch module/contracts and stop; do not start other modules.
```

### [E-05] crm
```
Branch: module/crm
Read tools/module-prompt-template.md and execute it exactly with ID=crm.
Source of truth: specs/crm.md. Also read Contracts of every module named in its requires/optional/consumes.
Integrate with other modules ONLY via events and Contracts; guard optional providers (nullable); never touch other modules' folders.
Open ONE PR from branch module/crm and stop; do not start other modules.
```

### [E-06] project-mgmt
```
Branch: module/project-mgmt
Read tools/module-prompt-template.md and execute it exactly with ID=project-mgmt.
Source of truth: specs/project-mgmt.md. Also read Contracts of every module named in its requires/optional/consumes.
Integrate with other modules ONLY via events and Contracts; guard optional providers (nullable); never touch other modules' folders.
Open ONE PR from branch module/project-mgmt and stop; do not start other modules.
```

### [E-07] hse
```
Branch: module/hse
Read tools/module-prompt-template.md and execute it exactly with ID=hse.
Source of truth: specs/hse.md. Also read Contracts of every module named in its requires/optional/consumes.
Integrate with other modules ONLY via events and Contracts; guard optional providers (nullable); never touch other modules' folders.
Open ONE PR from branch module/hse and stop; do not start other modules.
```

### [E-08] shipping-distribution
```
Branch: module/shipping-distribution
Read tools/module-prompt-template.md and execute it exactly with ID=shipping-distribution.
Source of truth: specs/shipping-distribution.md. Also read Contracts of every module named in its requires/optional/consumes.
Integrate with other modules ONLY via events and Contracts; guard optional providers (nullable); never touch other modules' folders.
Open ONE PR from branch module/shipping-distribution and stop; do not start other modules.
```

### [E-09] import-customs
```
Branch: module/import-customs
Read tools/module-prompt-template.md and execute it exactly with ID=import-customs.
Source of truth: specs/import-customs.md. Also read Contracts of every module named in its requires/optional/consumes.
Integrate with other modules ONLY via events and Contracts; guard optional providers (nullable); never touch other modules' folders.
Open ONE PR from branch module/import-customs and stop; do not start other modules.
```

### [E-10] forex-lc
```
Branch: module/forex-lc
Read tools/module-prompt-template.md and execute it exactly with ID=forex-lc.
Source of truth: specs/forex-lc.md. Also read Contracts of every module named in its requires/optional/consumes.
Integrate with other modules ONLY via events and Contracts; guard optional providers (nullable); never touch other modules' folders.
Open ONE PR from branch module/forex-lc and stop; do not start other modules.
```

### [E-11] supplier-evaluation
```
Branch: module/supplier-evaluation
Read tools/module-prompt-template.md and execute it exactly with ID=supplier-evaluation.
Source of truth: specs/supplier-evaluation.md. Also read Contracts of every module named in its requires/optional/consumes.
Integrate with other modules ONLY via events and Contracts; guard optional providers (nullable); never touch other modules' folders.
Open ONE PR from branch module/supplier-evaluation and stop; do not start other modules.
```

### [E-12] training-lms
```
Branch: module/training-lms
Read tools/module-prompt-template.md and execute it exactly with ID=training-lms.
Source of truth: specs/training-lms.md. Also read Contracts of every module named in its requires/optional/consumes.
Integrate with other modules ONLY via events and Contracts; guard optional providers (nullable); never touch other modules' folders.
Open ONE PR from branch module/training-lms and stop; do not start other modules.
```

### [E-13] recruitment
```
Branch: module/recruitment
Read tools/module-prompt-template.md and execute it exactly with ID=recruitment.
Source of truth: specs/recruitment.md. Also read Contracts of every module named in its requires/optional/consumes.
Integrate with other modules ONLY via events and Contracts; guard optional providers (nullable); never touch other modules' folders.
Open ONE PR from branch module/recruitment and stop; do not start other modules.
```

### [E-14] competency-appraisal
```
Branch: module/competency-appraisal
Read tools/module-prompt-template.md and execute it exactly with ID=competency-appraisal.
Source of truth: specs/competency-appraisal.md. Also read Contracts of every module named in its requires/optional/consumes.
Integrate with other modules ONLY via events and Contracts; guard optional providers (nullable); never touch other modules' folders.
Open ONE PR from branch module/competency-appraisal and stop; do not start other modules.
```

### [E-15] management-consolidation
```
Branch: module/management-consolidation
Read tools/module-prompt-template.md and execute it exactly with ID=management-consolidation.
Source of truth: specs/management-consolidation.md. Also read Contracts of every module named in its requires/optional/consumes.
Integrate with other modules ONLY via events and Contracts; guard optional providers (nullable); never touch other modules' folders.
Open ONE PR from branch module/management-consolidation and stop; do not start other modules.
```

### [E-16] audit-iso
```
Branch: module/audit-iso
Read tools/module-prompt-template.md and execute it exactly with ID=audit-iso.
Source of truth: specs/audit-iso.md. Also read Contracts of every module named in its requires/optional/consumes.
Integrate with other modules ONLY via events and Contracts; guard optional providers (nullable); never touch other modules' folders.
Open ONE PR from branch module/audit-iso and stop; do not start other modules.
```

### [E-17] condition-monitoring-iot
```
Branch: module/condition-monitoring-iot
Read tools/module-prompt-template.md and execute it exactly with ID=condition-monitoring-iot.
Source of truth: specs/condition-monitoring-iot.md. Also read Contracts of every module named in its requires/optional/consumes.
Integrate with other modules ONLY via events and Contracts; guard optional providers (nullable); never touch other modules' folders.
FOCUS: batched idempotent ingestion, rollups, retention/purge job, DB-size guard for shared hosting.
Open ONE PR from branch module/condition-monitoring-iot and stop; do not start other modules.
```


## GROUP F — Light/low-code modules. Can start together with E. Smaller PRs. (10 modules)

Group F policy: prefer form-builder / workflow-engine definitions shipped as JSON inside the module (see docs/low-code-modules.md); write custom code only where the spec requires (devices, scanning screens, SLA clocks, integrations).

### [F-01] ticketing
```
Branch: module/ticketing
Read tools/module-prompt-template.md and execute it exactly with ID=ticketing.
Source of truth: specs/ticketing.md. Also read Contracts of every module named in its requires/optional/consumes.
Integrate with other modules ONLY via events and Contracts; guard optional providers (nullable); never touch other modules' folders.
Open ONE PR from branch module/ticketing and stop; do not start other modules.
```

### [F-02] warranty-after-sales
```
Branch: module/warranty-after-sales
Read tools/module-prompt-template.md and execute it exactly with ID=warranty-after-sales.
Source of truth: specs/warranty-after-sales.md. Also read Contracts of every module named in its requires/optional/consumes.
Integrate with other modules ONLY via events and Contracts; guard optional providers (nullable); never touch other modules' folders.
Open ONE PR from branch module/warranty-after-sales and stop; do not start other modules.
```

### [F-03] correspondence
```
Branch: module/correspondence
Read tools/module-prompt-template.md and execute it exactly with ID=correspondence.
Source of truth: specs/correspondence.md. Also read Contracts of every module named in its requires/optional/consumes.
Integrate with other modules ONLY via events and Contracts; guard optional providers (nullable); never touch other modules' folders.
Open ONE PR from branch module/correspondence and stop; do not start other modules.
```

### [F-04] weighbridge
```
Branch: module/weighbridge
Read tools/module-prompt-template.md and execute it exactly with ID=weighbridge.
Source of truth: specs/weighbridge.md. Also read Contracts of every module named in its requires/optional/consumes.
Integrate with other modules ONLY via events and Contracts; guard optional providers (nullable); never touch other modules' folders.
FOCUS: console UX, one-pass/two-pass modes, tolerance check, immutable completed tickets, optional single-file local agent in modules/weighbridge/agent/.
Open ONE PR from branch module/weighbridge and stop; do not start other modules.
```

### [F-05] canteen
```
Branch: module/canteen
Read tools/module-prompt-template.md and execute it exactly with ID=canteen.
Source of truth: specs/canteen.md. Also read Contracts of every module named in its requires/optional/consumes.
Integrate with other modules ONLY via events and Contracts; guard optional providers (nullable); never touch other modules' folders.
Open ONE PR from branch module/canteen and stop; do not start other modules.
```

### [F-06] parking-vehicles
```
Branch: module/parking-vehicles
Read tools/module-prompt-template.md and execute it exactly with ID=parking-vehicles.
Source of truth: specs/parking-vehicles.md. Also read Contracts of every module named in its requires/optional/consumes.
Integrate with other modules ONLY via events and Contracts; guard optional providers (nullable); never touch other modules' folders.
Open ONE PR from branch module/parking-vehicles and stop; do not start other modules.
```

### [F-07] gate-visitor
```
Branch: module/gate-visitor
Read tools/module-prompt-template.md and execute it exactly with ID=gate-visitor.
Source of truth: specs/gate-visitor.md. Also read Contracts of every module named in its requires/optional/consumes.
Integrate with other modules ONLY via events and Contracts; guard optional providers (nullable); never touch other modules' folders.
Open ONE PR from branch module/gate-visitor and stop; do not start other modules.
```

### [F-08] energy-utilities
```
Branch: module/energy-utilities
Read tools/module-prompt-template.md and execute it exactly with ID=energy-utilities.
Source of truth: specs/energy-utilities.md. Also read Contracts of every module named in its requires/optional/consumes.
Integrate with other modules ONLY via events and Contracts; guard optional providers (nullable); never touch other modules' folders.
Open ONE PR from branch module/energy-utilities and stop; do not start other modules.
```

### [F-09] waste-environment
```
Branch: module/waste-environment
Read tools/module-prompt-template.md and execute it exactly with ID=waste-environment.
Source of truth: specs/waste-environment.md. Also read Contracts of every module named in its requires/optional/consumes.
Integrate with other modules ONLY via events and Contracts; guard optional providers (nullable); never touch other modules' folders.
Open ONE PR from branch module/waste-environment and stop; do not start other modules.
```

### [F-10] contractor-mgmt
```
Branch: module/contractor-mgmt
Read tools/module-prompt-template.md and execute it exactly with ID=contractor-mgmt.
Source of truth: specs/contractor-mgmt.md. Also read Contracts of every module named in its requires/optional/consumes.
Integrate with other modules ONLY via events and Contracts; guard optional providers (nullable); never touch other modules' folders.
Open ONE PR from branch module/contractor-mgmt and stop; do not start other modules.
```


## GROUP G — AI modules. Build last (needs datasets from other modules + ai-service skeleton). (6 modules)

Group G policy: each AI module = PHP side (manifest ai block, datasets, UI, proposal handling) + its handler in ai-service/handlers/<capability>.py + eval script + tests. Must work with provider=off and with a fake provider.

### [G-01] demand-forecast
```
Branch: module/demand-forecast
Read tools/module-prompt-template.md and execute it exactly with ID=demand-forecast.
Source of truth: specs/demand-forecast.md. Also read Contracts of every module named in its requires/optional/consumes.
Integrate with other modules ONLY via events and Contracts; guard optional providers (nullable); never touch other modules' folders.
FOCUS: series classification, rolling-origin backtests, WAPE metrics in tests, adoption flow into MPS/reorder as proposals.
Open ONE PR from branch module/demand-forecast and stop; do not start other modules.
```

### [G-02] failure-prediction
```
Branch: module/failure-prediction
Read tools/module-prompt-template.md and execute it exactly with ID=failure-prediction.
Source of truth: specs/failure-prediction.md. Also read Contracts of every module named in its requires/optional/consumes.
Integrate with other modules ONLY via events and Contracts; guard optional providers (nullable); never touch other modules' folders.
FOCUS: transparent baselines for cold start, explanations of drivers, feedback loop, minimum-quality gating.
Open ONE PR from branch module/failure-prediction and stop; do not start other modules.
```

### [G-03] anomaly-detection
```
Branch: module/anomaly-detection
Read tools/module-prompt-template.md and execute it exactly with ID=anomaly-detection.
Source of truth: specs/anomaly-detection.md. Also read Contracts of every module named in its requires/optional/consumes.
Integrate with other modules ONLY via events and Contracts; guard optional providers (nullable); never touch other modules' folders.
FOCUS: deterministic PHP rule engine (works without Python) + Python detectors, injected-anomaly tests, default profile packs.
Open ONE PR from branch module/anomaly-detection and stop; do not start other modules.
```

### [G-04] nl-report-assistant
```
Branch: module/nl-report-assistant
Read tools/module-prompt-template.md and execute it exactly with ID=nl-report-assistant.
Source of truth: specs/nl-report-assistant.md. Also read Contracts of every module named in its requires/optional/consumes.
Integrate with other modules ONLY via events and Contracts; guard optional providers (nullable); never touch other modules' folders.
FOCUS: LLM outputs QuerySpec JSON only (never SQL), validation against the permitted dataset catalog, Persian date phrases, golden-question tests with a fake LLM.
Open ONE PR from branch module/nl-report-assistant and stop; do not start other modules.
```

### [G-05] doc-ocr
```
Branch: module/doc-ocr
Read tools/module-prompt-template.md and execute it exactly with ID=doc-ocr.
Source of truth: specs/doc-ocr.md. Also read Contracts of every module named in its requires/optional/consumes.
Integrate with other modules ONLY via events and Contracts; guard optional providers (nullable); never touch other modules' folders.
FOCUS: Persian digit/char normalization, review UI, Tesseract fa+en pipeline in ai-service, proposals only.
Open ONE PR from branch module/doc-ocr and stop; do not start other modules.
```

### [G-06] schedule-optimizer
```
Branch: module/schedule-optimizer
Read tools/module-prompt-template.md and execute it exactly with ID=schedule-optimizer.
Source of truth: specs/schedule-optimizer.md. Also read Contracts of every module named in its requires/optional/consumes.
Integrate with other modules ONLY via events and Contracts; guard optional providers (nullable); never touch other modules' folders.
FOCUS: OR-Tools CP-SAT model with setup matrix/calendars/locked ops, benchmark instances in tests, results as draft schedule proposals.
Open ONE PR from branch module/schedule-optimizer and stop; do not start other modules.
```

# STAGE PROMPTS (run after the indicated groups are merged)

### [S1] Integration scenarios: Procure-to-Pay, Order-to-Cash, Hire-to-Pay (after A+B)
```
Branch: test/s1-integration-a-b
Read AGENTS.md, docs/event-catalog.md, docs/wave-plan.md. Create ONLY: tests/integration/**, tools/integration.php, docs/integration-report.md, docs/integration-issues.md.
Build an integration harness (CLI: php tools/integration.php <scenario|all>) that creates a temp DB, runs core + selected module migrations, seeds data ONLY through module services/APIs (never raw SQL), runs the outbox until idle, and asserts invariants.
Scenarios:
1) procure-to-pay: purchase-request -> approval -> rfq (3 quotes, award) -> purchase-order (approval by amount) -> inventory receipt (lot) -> supplier invoice 3-way match -> payment (cheque) -> journals. Assert: PO received/invoiced qty, stock balance & lot, AP balance, every journal balanced, party statement matches, events fired in order, audit entries exist.
2) order-to-cash: quotation -> sales order (credit check, reservation) -> delivery -> inventory issue -> invoice -> receipt (cash + cheque, bounced cheque path) -> journals. Assert stock, AR aging, credit exposure, COGS journal request present.
3) stock-control: stock count with variance approval, adjustment, transfer two-step, expiry/FEFO picking.
4) hire-to-pay: hire -> shifts -> attendance logs -> leave -> period close -> payroll run (sample tables) -> payment file -> journals.
5) permission-matrix: for each scenario step, verify a role without permission is denied (HTTP 403) and data-scope rules hold.
For each failure: if the fix is small and confined to one module's folder, fix it in a separate commit labeled with the module id; otherwise record in docs/integration-issues.md (module, symptom, repro, suspected cause). Report results in docs/integration-report.md. CI: add the scenarios (SQLite/MySQL as supported) as a job.
```

### [S2] Integration scenarios: Plan-to-Produce and Quality (after C+D)
```
Branch: test/s2-integration-c-d
Same harness rules as S1 (extend tools/integration.php, tests/integration/**, docs reports).
Scenarios:
1) plan-to-produce: sales demand + forecast -> MPS -> MRP (multi-level BOM, safety stock, lead times) -> planned orders -> firm -> purchase-request drafts and work orders -> release -> material issue (backflush + manual) -> MES operator reports (good/scrap/downtime) -> output receipt (finished + by-product + scrap) -> work order close -> costing run (standard vs actual, variances, WIP) -> journals. Assert MRP pegging, WO quantities, stock, OEE inputs, cost totals vs hand-computed expected values, balanced journals.
2) quality-loop: IQC lot on receipt -> reject -> stock bucket blocked -> NCR -> SCAR -> CAPA -> supplier score change; IPQC prompt from MES; lab sample/COA through LIMS; calibration overdue instrument blocks usage.
3) recall: lot genealogy forward/backward through receipt -> WO -> delivery; recall scope, quarantine of on-hand lots, affected customers list.
4) maintenance-loop: asset meter -> PM generation -> WO with parts issue -> downtime -> OEE/availability update -> failure code stats.
5) scheduling: finite-capacity schedule respects calendars, setup matrix, maintenance windows; publish -> MES dispatch.
Failures: same policy as S1 (small fixes in a module folder as separate commits; others into docs/integration-issues.md).
```

### [S3] Security review and static linters (after B; re-run at the end)
```
Branch: hardening/s3-security
Create tools/lint-security.php (+ CI job) with static checks over core/ and modules/**: routes without permission guard; string-concatenated SQL; queries touching tables outside the module prefix; mutations without audit/event; file uploads without type/size checks; use of eval/unserialize/exec; missing CSRF on cookie-session mutations; secrets in repo; AI tool definitions that bypass permission checks; manifests whose permissions are not enforced by any route.
Then perform a manual review of auth-rbac, api-webhooks, document-mgmt, notifications, ai gateway and the 5 most sensitive modules (payroll, hr-core, treasury, accounting-gl, tax-moadian). Write docs/security-review.md (findings by severity, evidence, fix status). Fix findings confined to one module as separate commits per module; list the rest as issues. Do not weaken any existing check to make the linter pass.
```

### [S4] Builder end-to-end, load and performance (after A..D)
```
Branch: hardening/s4-build-perf
1) Build ALL profiles (minimal, standard, full, example-steel, example-food) via builder; for each: install install.sql on an empty MySQL/MariaDB, boot the app, call /api/_health and /api/_meta/modules, run smoke tests, record zip size, file count, build time. Fail if a ticked module's migrations break or an unticked module leaks into the package.
2) Create tools/seed-load.php generating synthetic data through services (items 50k, stock movements 500k, journals 200k, WO 20k) with a size parameter; benchmark key endpoints and reports; run EXPLAIN on the 30 heaviest queries; add missing indexes via module migrations (each in its own module PR commit).
3) Write docs/performance.md with measurements and cPanel-specific limits (memory, max_execution_time, cron cadence for events:work), and make long jobs (MRP, cost run, reports, imports) provably chunked/resumable.
4) Add CI job building the minimal profile and reporting size/time.
```

### [S5] Persian documentation and "define the factory's processes" guide (after A..F)
```
Branch: docs/s5-docs
Generate from manifests/specs (script tools/gen-docs.php, committed output in docs/user/): (a) catalog of all modules with Persian name, purpose, dependencies, screens, permissions, events; (b) INSTALL-FA.md (cPanel + XAMPP + Docker), (c) ADMIN-GUIDE-FA.md (users/roles, companies, numbering, settings, AI switches, backups, cron), (d) docs/process-definition-guide.md: how to onboard a new factory using ONLY configuration: choose profile, tick modules, terminology overrides, workflows and forms (form-builder/workflow-engine definitions), print templates, posting rules, approval chains, dynamic fields; include 3 worked examples (purchase approval chain, quality inspection plan, maintenance request flow), (e) one-page quick reference per module for end users (generated), (f) glossary FA/EN of all domain terms.
Keep every file concise; no marketing text.
```

### [S6] AI end-to-end verification (after G)
```
Branch: hardening/s6-ai
Verify every module's `ai` block: capabilities declared, tools exist and are read-only, proposals only, redaction fields exist in the entity/dataset, provider=off path works. Add tests: gateway permission enforcement (AI cannot exceed user permissions), 3-level enable switches, budget/token limits, redaction before external call, audit completeness, fake-provider happy paths for assistant/autofill/anomaly/next_action in a sample of 10 modules, PHP->Python path with the FastAPI skeleton running (signature failure, timeout, malformed response handling). Build an admin "AI settings" screen if missing (global, per-module, per-capability toggles, provider, budgets, usage). Write docs/ai-verification.md.
```

### [S7] i18n / RTL / UX consistency sweep (after A..F)
```
Branch: hardening/s7-i18n-ux
Create tools/i18n-check.php (+ CI): find hard-coded UI strings, missing fa.json keys per module, Gregorian dates shown without Jalali, numbers without locale formatting, LTR-only layout bugs, missing empty/loading/error states. Fix by editing each module's web folder in separate commits per module. Add a UX checklist doc (docs/ux-checklist.md) and verify the 15 most used screens at mobile/tablet/desktop widths with screenshots-by-script if possible (Playwright optional, otherwise component tests).
```

### [S8] First real factory: industry plugin + profile (run when the industry is known)
```
Branch: plugin/<industry>
Read docs/process-definition-guide.md and plugins/generic. Given the factory description I paste below, create plugins/<industry>/plugin.json, a profile profiles/<factory>.yaml (ticked modules + AI switches), terminology overrides, dynamic-field sets for items/assets/quality characteristics, workflow/form definition overrides, print templates and a short plugin README. Prefer configuration over code; only create plugin code where configuration cannot express the need, and list those gaps. Factory description: <PASTE HERE>
```
