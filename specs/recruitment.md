# recruitment — Hiring pipeline (L2; requires hr-core; optional org-structure, notifications)

**Entities** JobRequisition{position_id,headcount,reason,requested_by,status[draft|approved|open|filled|cancelled]}; JobPosting{title,description,requirements,channels}; Candidate{names,phone,email,national_code(optional),source,resume_doc,status,tags}; Application{candidate,requisition,stage[applied|screened|interview|test|offer|hired|rejected],scores,notes}; Interview{date,panel,type,scorecard}; Offer{terms(ref only; salary details limited),status}; Onboarding handoff.
**Rules** requisition approval vs headcount in org-structure; duplicate candidate detection (national code/phone/email normalization); stage transitions with required scorecards; GDPR-like retention setting for rejected candidates; hire → creates hr-core employee draft via event/service; blacklist/talent-pool flags.
**Events out** recruitment.application.hired, recruitment.requisition.filled. **In** hr-core.employee.hired (close loop).
**Service contract** RecruitmentService: pipeline(requisition), sourceStats(range), timeToHire(range).
**Permissions** recruitment.requisition|candidate|interview|offer.view|create|update|approve, recruitment.candidate.sensitive.view.
**Screens** requisitions, pipeline kanban, candidate profile, interview scheduling/scorecards, offers, analytics.
**AI** autofill (parse CV text into candidate fields, proposal), screening summary vs requirements (advisory only, with bias warning; never auto-reject), summarize interviews. redact: personal ids. **Datasets** pipeline_stats.
**Iran** Persian resumes, military service status field (visible to HR only).
