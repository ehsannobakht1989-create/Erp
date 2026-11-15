# contracts — Contract management (L5; requires party-master, document-mgmt; optional purchase-order, pricing, contractor-mgmt, budgeting)

**Entities** Contract{number,title,type[purchase|sales|service|lease|employment_related|nda|other],party_id,start,end,value,currency,status[draft|review|approved|active|expiring|expired|terminated],auto_renew,renewal_notice_days,owner_id,template_id}; ClauseLibrary{code,title,text(with variables)}; ContractTemplate; Milestone/PaymentSchedule{date,amount,condition,status}; Obligation{description,owner,due,status}; Amendment; ContractDocument versions; LinkedRef{po|price_list|blanket_order}.
**Rules** generate contract text from template+variables (print template), approvals via workflow, expiry/renewal alerts, amendments keep history, links create blanket PO / price list suggestions via events (not direct writes), milestones raise payment requests (event for ar-ap), value tracking vs invoiced (ArApService optional).
**Events out** contracts.contract.activated|expiring|terminated, contracts.milestone.due. **In** ar-ap.invoice.posted (consumed value), purchase-order.po.* (usage).
**Service contract** ContractService: activeFor(party,type,date), consumed(contractId), expiring(days), terms(contractId).
**Permissions** contracts.contract.view|create|update|approve|terminate, contracts.clause.manage, contracts.template.manage.
**Screens** register, contract form + clause picker, timeline with milestones, expiry board, obligations tracker, print/preview.
**AI** summarize contract, extract key terms from pasted/uploaded text (doc-ocr, proposal), anomaly (unusual clauses vs library), next_action. **Datasets** contracts_register.
**Iran** Persian legal templates as editable data (not legal advice), Jalali dates.
