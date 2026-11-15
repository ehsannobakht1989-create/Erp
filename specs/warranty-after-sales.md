# warranty-after-sales — Warranty & service (L3; requires sales; optional lot-traceability, inventory, ncr-capa, party-master)

**Entities** WarrantyTerm{item|category,duration,type[time|usage],conditions}; WarrantyRegistration{item,serial|lot,customer,delivery_ref,start,end,status}; Claim(RMA){number,registration,reported_issue,date,status[new|received|diagnosing|approved|repairing|replaced|credited|rejected|closed],diagnosis,decision,cost,parts_used[],labor_hours}; ServiceVisit{claim,date,technician,notes}; ReturnShipment refs.
**Rules** registrations auto-created from `sales.delivery.posted` for items with warranty terms (serial/lot from inventory); eligibility check at claim (dates, usage, exclusions); decisions: repair (parts via InventoryService issue), replace (sales return/replacement order via events), credit (credit note request); failure analysis creates NCR/CAPA proposal; cost reports per product; spare-parts sales link.
**Events out** warranty-after-sales.claim.approved|closed, warranty-after-sales.credit.requested. **In** sales.delivery.posted, sales.return.posted.
**Service contract** WarrantyService: isUnderWarranty(item,serial|lot), claimsFor(customer), failureStats(item,range).
**Permissions** warranty-after-sales.term|registration|claim.view|create|update|approve|close.
**Screens** claim board, claim form with eligibility panel, registrations lookup (scan/serial), failure Pareto, warranty cost report, print forms.
**AI** summarize claim, anomaly (batch quality issues), next_action. **Datasets** claims.
