# crm — Customer relationship management (L2; requires party-master; optional sales, notifications)

**Entities** Lead{source,status,score,owner,contact info,company_name,needs,converted_party_id}; Opportunity{party,stage,amount,probability,expected_close,owner,competitors,lost_reason}; PipelineStage config; Activity{type[call|meeting|email|task|visit|note],subject,due,done,owner,party/lead/opportunity ref}; Campaign{channel,audience(segment query via dataset),status,stats}; Segment; Interaction timeline (derived from events: quotes, orders, tickets); Target{owner,period,amount}.
**Rules** lead→party+contact+opportunity conversion (calls PartyService contract / event; never writes party tables), duplicate detection by phone/email/name normalization, stage gates, probability defaults, SLA follow-up reminders via notifications, ownership/round-robin assignment rules, quotation creation hands off to sales via event/service.
**Events out** crm.lead.created|converted, crm.opportunity.won|lost. **In** sales.quotation.*|order.*, ticketing.ticket.created (timeline).
**Service contract** CrmService: customer360(partyId), openOpportunities(owner?), pipeline(range).
**Permissions** crm.lead|opportunity|activity|campaign.view|create|update|delete, crm.pipeline.manage, crm.target.manage.
**Screens** kanban pipeline, lead list/form, activity calendar, customer 360, campaign builder + SMS/Telegram via notifications, forecast view, targets dashboard, mobile-friendly quick log.
**AI** lead scoring (python/avalai, proposals), next_action, summarize account, autofill (leads from pasted messages), forecast pipeline. **Datasets** pipeline, activities.
**Iran** Persian name normalization, mobile numbers format validation (+98/09xx), Telegram/SMS campaigns.
