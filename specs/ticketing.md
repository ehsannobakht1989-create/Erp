# ticketing — Helpdesk & service requests (L2; optional crm, asset-registry, org-structure; low-code + SLA)

**Entities** Queue{name,type[it|facility|hr|customer|custom],sla_policy,assignment_rule}; Ticket{number,title,description,requester(user|party contact),category,priority,status[new|open|pending|resolved|closed],assignee,queue,related_asset,related_party,channel,due_at}; Comment{public|internal,attachments}; SlaPolicy{response_min,resolve_min,business_hours_calendar,pause_statuses}; KbArticle{title,body,tags,status}; Csat{ticket,score,comment}.
**Rules** SLA clocks with business-hours calendar and pausing; auto-assign (round-robin/least-loaded/skill) and escalation levels; merge/split; e-mail-to-ticket adapter interface (mock); reply templates; KB suggestions by text similarity.
**Events out** ticketing.ticket.created|resolved|sla_breached. **In** cmms.workorder.* (link), crm.* (customer tickets).
**Service contract** TicketService: openFor(party|asset), slaStats(range).
**Permissions** ticketing.ticket|queue|kb.view|create|update|assign|close, ticketing.sla.manage.
**Screens** my tickets, queue board, ticket detail, portal-style submit form, SLA dashboard, KB.
**AI** classify/route ticket, suggest reply from KB (proposal), summarize thread, anomaly (spikes). **Datasets** tickets, sla.
