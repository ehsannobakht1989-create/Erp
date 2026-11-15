# hr-core — Employees & employment (L2; requires org-structure)

**Entities** Employee{personnel_no,national_code,first_name,last_name,father_name,birth_date,birth_cert_no,gender,marital_status,military_status,nationality,phones,email,address,photo,status[candidate|active|on_leave|terminated]}; Employment{employee_id,contract_type[permanent|fixed|project|daily|part_time],start,end,position_id,unit_id,manager_id,job_title,grade,probation_end,insurance_no,insurance_type,work_location}; EmploymentEvent{type[hire|transfer|promotion|grade_change|contract_renewal|termination|rehire],effective_date,data,reason}; BankAccount; Education; Experience; Dependent; EmployeeDocument; EmergencyContact.
**Rules** national code checksum; unique personnel_no/national_code; changes are effective-dated (history table, no overwrite); salary data NOT stored here (payroll owns) — hr only stores grade; termination requires reason & clearance checklist (workflow: manager→HR→finance); sensitive fields visible by permission; manager chain from org-structure.
**Events out** hr-core.employee.hired|transferred|promoted|terminated|updated. **In** none required.
**Service contract** HrService: employee(id), byUser(userId), byPersonnelNo(), lookup(q), subordinates(managerId,depth), activeEmployees(filter,asOf), employmentAt(employee,date), isActive(id).
**Permissions** hr-core.employee.view|create|update|terminate|export, hr-core.sensitive.view, hr-core.event.manage.
**Screens** employee list/cards, profile (tabs), onboarding/offboarding checklists (workflow), org chart link, headcount dashboard, expiring contracts, birthdays widget, import.
**AI** assistant on non-sensitive HR queries (headcount, tenure), autofill from CV text (proposal). redact: national_code, bank, phones, birth, insurance.
**Iran** مدارک: شناسنامه/کارت ملی/پایان خدمت fields, insurance (تامین اجتماعی) number, Jalali dates.
