# forex-lc — Foreign exchange & letters of credit (L3; regional ir; requires treasury, accounting-gl; optional import-customs, purchase-order)

**Entities** ForexAccount{bank,currency,balance_ref}; FxDeal{type[buy|sell|remittance],currency,amount,rate,source_type(data list),date,counterparty,status,settlement_refs}; LetterOfCredit{number,type[sight|usance|…data],applicant/beneficiary,currency,amount,issuing_bank,advising_bank,expiry,latest_shipment,tolerance,terms,status[draft|opened|amended|shipped|docs_presented|accepted|paid|expired|closed],po_ref,import_file_ref}; LCAmendment; LCDocumentSet{docs[],discrepancies[]}; BankGuarantee{direction[issued|received],type,amount,beneficiary,expiry,status,fee}; FxRate history (from multi-company); Fee/Commission lines.
**Rules** LC utilization tracked against PO/shipments; amendment workflow; discrepancy handling; expiry/latest shipment alerts; guarantee expiry alerts; settlement creates treasury payment orders and journal requests with FX gain/loss using rates; exposure report (open LC + unpaid foreign invoices) by currency/maturity; periodic revaluation inputs to accounting-gl.
**Events out** forex-lc.lc.status_changed, forex-lc.guarantee.expiring, forex-lc.fx.settled. **In** purchase-order.po.approved, import-customs.file.*, ar-ap.invoice.posted(foreign), treasury.payment.executed.
**Service contract** ForexService: exposure(currency,asOf), lcFor(po), guaranteesExpiring(days), rateAt(currency,date).
**Permissions** forex-lc.lc|fx|guarantee.view|create|update|approve|settle.
**Screens** LC list/board, LC form with amendments, document presentation checklist, FX deals, guarantees register + expiry board, exposure dashboard, rates.
**AI** extract LC terms from text (doc-ocr, proposal), forecast exposure, anomaly. **Datasets** lc_register, fx_exposure.
**Iran** اعتبار اسنادی/ضمانت‌نامه/حواله ارزی terminology, multi-rate types (data-driven), Jalali dates.
