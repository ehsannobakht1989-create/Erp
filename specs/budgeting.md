# budgeting — Budgets & commitment control (L5; requires accounting-gl, org-structure; optional purchase-request, purchase-order, project-mgmt)

**Entities** BudgetVersion{year,name,status[draft|submitted|approved|locked|superseded],basis}; BudgetLine{version,account|account_group,dimension values(cost_center,project),period amounts (12 Jalali months),currency,notes}; Revision; Transfer{from_line,to_line,amount,reason,status}; Encumbrance{source[pr|po|contract],ref,amount,date,status}; Rolling forecast lines; Template.
**Rules** approval by workflow per cost-center owner then finance; budget check API: available = approved budget − actuals (AccountingService) − open encumbrances; setting warn|block|ignore per account group/cost center; encumbrance auto-created/released from PR/PO events; transfers need approval within policy; import/export; versions comparison; period locking.
**Events out** budgeting.budget.approved, budgeting.check.exceeded. **In** purchase-request.pr.approved|cancelled, purchase-order.po.approved|closed|cancelled, accounting-gl.journal.posted.
**Service contract** BudgetService: check(CheckRequest{dims,account,amount,date}) → {available, status[ok|warn|block], explain}, commit(ref,amount), release(ref), variance(scope,period).
**Permissions** budgeting.budget.view|edit|approve, budgeting.transfer.request|approve, budgeting.check.override.
**Screens** budget grid by dimension×months (paste from Excel), versions & compare, variance report (budget vs actual vs committed), transfer requests, commitment ledger, owner dashboard.
**AI** autofill (draft next year from actuals ± drivers, proposal), forecast spend (python), anomaly (overruns), nl_report. **Datasets** budget_vs_actual, commitments.
**Iran** Jalali fiscal periods; inflation factor helper in drafting.
