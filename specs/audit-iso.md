# audit-iso — Management systems & audits (L5; requires document-mgmt; optional ncr-capa, hr-core, supplier-evaluation)

**Entities** Standard{code,title,clauses[tree]} (data: ISO 9001/14001/45001/others importable); ControlledDocument link (via document-mgmt; approval/revision workflow reference); AuditProgram{year,scope}; Audit{type[internal|supplier|external|certification],date,scope,auditors,checklist(responses),status}; Finding{type[major_nc|minor_nc|observation|opportunity],clause,evidence,owner,due,ncr_ref}; ManagementReview{date,agenda,inputs,minutes,actions[]}; Objective{title,kpi_ref,target,owner}; RiskOpportunity register{context,risk,likelihood,impact,controls,review}; LegalRequirement register (shared with hse optional).
**Rules** audit scheduling with auditor independence rule; checklist built from clauses; findings create NCR/CAPA via events; management review pulls KPIs from report-bi datasets; document review dates alerts; objectives progress auto-updated from KPI.
**Events out** audit-iso.audit.completed, audit-iso.finding.raised. **In** ncr-capa.capa.verified, report-bi.kpi.breached.
**Service contract** AuditService: programStatus(year), openFindings(), upcomingAudits(days).
**Permissions** audit-iso.audit|finding|review|risk|objective.view|manage, audit-iso.standard.manage.
**Screens** audit calendar, checklist runner (mobile), findings board, management review minutes with action items, risk register heatmap, objectives dashboard, clause coverage map.
**AI** autofill (checklist from clauses/scope, proposal), summarize audits, anomaly (repeat findings). **Datasets** findings, audits.
