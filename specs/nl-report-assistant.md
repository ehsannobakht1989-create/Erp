# nl-report-assistant — Natural-language analytics (L6; LLM via AvalAI; requires report-bi; python not required)

**Purpose** ask questions in Persian/English and get validated reports/charts over the dataset catalog; also a conversational assistant surface for any module's read-only tools.
**Entities** Conversation{user,module_scope,messages[],pinned}; QuerySpec{dataset,fields,filters,group_by,order,limit,chart,explain}; SavedQuestion{title,spec,owner,shared}; Feedback{message,rating,correction}.
**Rules** LLM NEVER writes SQL: it must output a **QuerySpec JSON** validated against the user's permitted dataset catalog (fields, types, params); validation failures trigger one guided retry then a clarifying question; execution through ReportService with the user's permissions & row-level scope; answers show the spec/filters used ("how was this computed"), row counts and limits; ambiguity handling (ask which period/measure); Persian date phrases ("ماه گذشته", "سه‌ماهه دوم امسال") resolved with Jalali calendar utilities; cost/token budgets and redaction rules from AI Gateway apply; conversation memory limited to the session; save answer as report/dashboard widget; tool-calling mode for module assistants uses only manifest-declared read-only tools.
**Events out** nl-report-assistant.question.saved.
**Service contract** NlReportService: ask(conversationId, message, user): AssistantReply{text, spec?, result?, followups[]}, validateSpec(spec,user), save(spec).
**Permissions** nl-report-assistant.use, nl-report-assistant.save, nl-report-assistant.admin.
**Screens** chat drawer available app-wide (module-context aware), result card (table/chart + "show query"), saved questions, admin: usage/cost, problem questions review.
**Eval** golden question set (Persian) per dataset family with expected specs; run with a fake LLM in CI and real provider optionally.
