# party-master — Customers, suppliers & other parties (L1; requires: platform)

**Purpose** single registry of external parties used by purchasing, sales, finance, treasury.
**Entities** Party{code, type[customer|supplier|both|contractor|other], legal_kind[natural|legal], name_fa, name_en, trade_name, national_id(شناسه ملی 11d / کد ملی 10d), economic_code(کد اقتصادی), registration_no, postal_code, phone/email/website, group_id, status[draft|pending|active|on_hold|blacklisted], currency, payment_terms_id, credit_limit, default_price_list_id(ref), rating, notes}; PartyAddress[]{type,province,city,address,postal_code,geo,is_default}; PartyContact[]{name,role,phone,mobile,email}; PartyBankAccount[]{bank,iban(شبا),account_no,card_no,holder,is_default}; PartyGroup(tree); PaymentTerm{code,name,days,installments[]}; PartyDocument via document-mgmt.
**Workflow** new supplier: draft→pending_approval→active; customer: direct activate (setting). active↔on_hold; blacklisted requires reason + audit.
**Rules** validate Iranian national id checksum (10d) and legal-entity id (11d) and IBAN (IR + mod97); unique national_id/economic_code per company (warn/override with reason); normalized-name duplicate detection (reuse item-master TextNormalizer if enabled, else local copy); credit limit non-negative; cannot delete used parties; one default address/bank.
**Events out** party-master.party.created|updated|activated|blacklisted|merged. **In** *.party.used (lock deletion).
**Service contract** PartyService: find(id), findByNationalId(), lookup(q,type), isActive(id), getAddresses(id), getDefaultBank(id), getCreditLimit(id), getPaymentTerm(id), validateIds(dto), markUsed(id,module).
**Permissions** party-master.party.view|create|update|approve|delete|merge|import|export, party-master.group.manage, party-master.payment-term.manage.
**Screens** list/filters/saved views, form tabs (General, Addresses, Contacts, Banks, Commercial, Documents, History), group tree, payment terms, import wizard (dry-run), duplicate review, merge tool, widgets (new this month, pending approval).
**AI** autofill from business card / invoice text (proposal), duplicate finder explanation, summarize party history. redact: bank accounts, credit_limit when sent to external provider.
**Iran** Persian text normalization (ی/ک/digits), province/city lists as seeded data (small), Sheba validation, Jalali dates.
