# import-customs — Import & clearance (L3; regional ir; requires purchase-order; optional forex-lc, costing, party-master)

**Entities** ImportFile{number,po_refs[],supplier,order_registration_no(ثبت سفارش),currency,forex_type[free|negotiated|…as data],proforma_no,status[planned|ordered|shipped|arrived|clearing|cleared|delivered|closed]}; Shipment{BL_no,vessel/flight,ports,etd,eta,containers[],incoterm,documents[CI,PL,BL,COO,inspection cert]}; CustomsDeclaration{declaration_no,hs_codes lines,customs_value,duties[],taxes[],fees[],status}; ClearanceCost{type[duty|tax|port|storage|demurrage|transport|broker|insurance|other],amount,currency,paid_by,doc}; Milestone log; LandedCostPack{lines allocation basis}.
**Rules** duty/tax rates and HS code lists are DATA tables maintained by the user; demurrage/storage clocks with free-days config and alerts; documents checklist per file with completeness gate before clearance; costs roll to a LandedCostPack sent to purchase-order charges / costing via event; goods receipt coordination with inventory (partial shipments); multi-PO per file and multi-file per PO.
**Events out** import-customs.file.cleared, import-customs.landed_cost.ready, import-customs.demurrage.alert. **In** purchase-order.po.approved, forex-lc.lc.status_changed, inventory.receipt.posted.
**Service contract** ImportService: fileForPo(po), eta(po), landedCost(fileId), openFiles().
**Permissions** import-customs.file|shipment|declaration|cost.view|create|update|close.
**Screens** import file board (kanban by stage), file form tabs (Docs, Shipment, Customs, Costs, Receipts), document checklist, ETA calendar, cost allocation, demurrage dashboard.
**AI** extract data from shipping docs (doc-ocr, proposal), anomaly (cost vs history), forecast arrival delays. **Datasets** import_files, clearance_costs.
**Iran** ثبت سفارش, ترخیص, اظهارنامه, قبض انبار, گواهی بازرسی fields only (no integrations).
