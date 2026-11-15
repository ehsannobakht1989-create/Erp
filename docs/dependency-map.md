# Dependency Map & Layer Model

Modules are organized into 7 strict layers (`L0` to `L6`). A module may ONLY depend (`requires`) on modules in **lower** layers (or explicitly acyclic peers in the same layer where unavoidable, never forming cycles).

## Layers

- **L0 — Platform (always on)**:
  `auth-rbac`, `multi-company`, `audit-log`, `dynamic-fields`, `workflow-engine`, `form-builder`, `notifications`, `document-mgmt`, `print-barcode-qr`, `api-webhooks`, `report-bi`
- **L1 — Master Data**:
  `item-master`, `party-master`, `org-structure`
- **L2 — Core Operations**:
  `inventory` (`item-master`), `purchase-request`, `rfq`, `purchase-order` (`item-master`, `party-master`, `inventory`), `sales` (`item-master`, `party-master`, `inventory`), `pricing` (`item-master`, `party-master`), `hr-core` (`org-structure`), `attendance` (`org-structure`), `asset-registry`, `hse`, `crm` (`party-master`), `project-mgmt`, `ticketing`
- **L3 — Engineering, Finance & Maintenance**:
  `bom` (`item-master`), `routing` (`item-master`), `accounting-gl`, `ar-ap` (`party-master`, `accounting-gl`), `treasury` (`accounting-gl`), `fixed-assets` (`asset-registry`, `accounting-gl`), `payroll` (`hr-core`, `attendance`, `accounting-gl`), `cmms` (`asset-registry`, `inventory`), `quality-inspection` (`inventory`)
- **L4 — Manufacturing Execution & Quality**:
  `work-order` (`bom`, `routing`, `inventory`), `mrp` (`bom`, `inventory`, `purchase-order`, `sales`), `mps` (`bom`, `inventory`, `sales`), `scheduling` (`work-order`, `routing`, `asset-registry`), `mes` (`work-order`, `asset-registry`), `oee` (`asset-registry`, `mes`), `batch-formulation` (`bom`, `inventory`), `tooling` (`asset-registry`, `inventory`), `scrap-byproduct` (`work-order`, `inventory`), `ncr-capa` (`quality-inspection`), `lims` (`quality-inspection`), `lot-traceability` (`inventory`, `work-order`), `reorder-point` (`inventory`)
- **L5 — Costing, Compliance & Advanced**:
  `costing` (`work-order`, `inventory`, `payroll`, `fixed-assets`, `accounting-gl`), `budgeting` (`accounting-gl`), `tax-moadian` (`sales`, `accounting-gl`), `eco-plm` (`bom`, `routing`), `condition-monitoring-iot` (`asset-registry`, `mes`)
- **L6 — AI Capabilities (Python, all optional)**:
  `demand-forecast` (`sales`, `inventory`), `failure-prediction` (`cmms`, `asset-registry`), `anomaly-detection` (`accounting-gl`, `inventory`), `nl-report-assistant` (`report-bi`), `doc-ocr` (`document-mgmt`), `schedule-optimizer` (`scheduling`)

## Support Modules (Form-Builder + Workflow-Engine)
Built primarily on L0 engines (`form-builder`, `workflow-engine`, `document-mgmt`) and placed in L2–L3:
`weighbridge`, `canteen`, `parking-vehicles`, `gate-visitor`, `correspondence`, `warranty-after-sales`, `energy-utilities`, `waste-environment`, `management-consolidation`, `contractor-mgmt`, `supplier-evaluation`, `contracts`, `import-customs`, `forex-lc`, `shipping-distribution`, `audit-iso`, `recruitment`, `training-lms`, `competency-appraisal`.
