# rfq — Inquiry & quotation comparison (L2; requires purchase-request, party-master)

**Entities** RFQ{number,deadline,status[draft|sent|collecting|evaluating|awarded|cancelled],pr_line_refs,terms,currency}; RFQLine; RFQInvitation{supplier_id,sent_at,channel,status}; Quote{supplier_id,rfq_id,validity,currency,payment_terms,delivery_days,incoterm,notes,attachments}; QuoteLine{rfq_line_id,price,qty_tiers[],lead_time,brand,alt_offer}; EvaluationCriteria{price,delivery,payment,quality_score,weights}; Award{rfq_line_id,quote_line_id,qty}.
**Rules** quotes accepted until deadline (override by permission); comparison normalizes currency/UoM/payment terms (net-present-value option); weighted scoring using supplier rating from supplier-evaluation if enabled; awarding requires approval by amount; awarded lines can split between suppliers; revision rounds supported; losing suppliers notified (notifications).
**Events out** rfq.rfq.sent, rfq.awarded{lines→supplier,price}. **In** purchase-request.pr.approved (lines to select).
**Service contract** RfqService: create(fromPrLines), getAward(rfqId), bestPrice(item,supplier?), history(item).
**Permissions** rfq.rfq.view|create|send|evaluate|award, rfq.quote.enter.
**Screens** RFQ board, create from PR workbench, quote entry grid, comparison matrix (heatmap, per-line cheapest, weighted score), award dialog → create PO(s) button, supplier history of quotes.
**AI** autofill/extract quotes from pasted text/PDF (via doc-ocr if enabled; proposal), recommend award with explanation (proposal), anomaly (suspiciously similar quotes).
**Iran** quotes often in rial/toman and USD/AED/CNY; show effective rate; attachments of proforma.
