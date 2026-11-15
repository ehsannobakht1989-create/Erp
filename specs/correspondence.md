# correspondence — Letters & secretariat (L2; requires document-mgmt, org-structure; low-code + numbering)

**Entities** Letter{kind[incoming|outgoing|internal],number(per Jalali year/sequence/format),date,subject,sender,recipient,confidentiality,priority,body|attachment,status[draft|registered|referred|answered|archived],related_letter_id}; Referral{letter,from,to,action[for_action|for_info|reply],note,due,status}; Template{title,body with variables,letterhead}; ArchiveFolder; Tag; FollowUp{letter,due,owner}.
**Rules** numbering via NumberingService with Jalali year reset; referral chain tracked; confidentiality by role/unit; reply creates linked outgoing; full-text search on subject/body/tags (and extracted text via document-mgmt when available); approval stamp workflow for outgoing letters; print with letterhead via print templates; deadlines alerts.
**Events out** correspondence.letter.registered|referred|overdue.
**Service contract** CorrespondenceService: inbox(user), byNumber(number), overdue().
**Permissions** correspondence.letter.view|create|refer|approve|archive, correspondence.confidential.view, correspondence.template.manage.
**Screens** secretariat register, my inbox/referrals, letter composer with templates, archive explorer, reports.
**AI** summarize letter, suggest referral/reply draft (proposal), classify. **Datasets** letters_stats.
**Iran** شماره ثبت/ارجاع/رونوشت/پیوست, Jalali dates, letterhead print layouts.
