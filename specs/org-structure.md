# org-structure — Organization, cost centers & calendars (L1)

**Entities** OrgUnit(tree){type[company|plant|department|section|line|warehouse_group], code, name, manager_position_id, cost_center_id}; Position{code,title,unit_id,reports_to_position_id,headcount}; PositionAssignment{employee_ref,position_id,from,to,is_primary}; CostCenter{code,name,owner_user_id,type[production|service|admin|project],parent}; WorkCalendar{code,weekly_pattern,week_start(Saturday default),holidays[]}; Holiday{date,name,type[official|company]}; Shift{code,name,start,end,break_minutes,crosses_midnight}; Location{code,name,type[plant|site|building]}.
**Rules** no cycles in trees; position assignment date overlaps only if not primary; calendar must have weekly pattern; Iranian official holidays seeded per Jalali year via importable CSV (admin-maintained, not hard-coded); shift crossing midnight handled in minute math.
**Events out** org-structure.unit.changed, position.assigned|vacated, cost-center.created|updated. **In** hr-core.employee.transferred (optional).
**Service contract** OrgService: unitsTree(), unit(id), managerOf(unitId|positionId), approverChain(userOrPositionId,levels), costCenter(id), costCenters(filter), isWorkday(calendarId,date), workingMinutes(calendarId,from,to), shift(id), shiftsFor(unitId).
**Permissions** org-structure.unit|position|cost-center|calendar|shift.view|manage.
**Screens** org chart (tree + search), positions, assignments, cost centers, calendar (Jalali month view, holiday import), shifts.
**AI** assistant: "who approves purchases for the press line?" using read-only tools; anomaly: orphan units/positions. proposals only.
**Iran** Saturday-first week, Friday weekend, Jalali holiday calendar with lunar-based religious holidays imported as data.
