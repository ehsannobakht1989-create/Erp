# project-mgmt — Projects & tasks (L2; optional budgeting, hr-core, sales, accounting-gl)

**Entities** Project{code,name,customer_id?,manager,start,end,status,budget_ref,cost_center_id,priority,health}; WBS/Task{parent,name,assignee,start,end,duration,progress,priority,status}; Dependency{from,to,type[FS|SS|FF|SF],lag}; Milestone; Resource{employee_ref|role,rate,capacity}; Timesheet{employee,date,task,hours,status}; Issue/Risk{severity,probability,owner,mitigation,status}; StatusReport; ProjectCostLink (actual cost from GL dimension `project` via AccountingService).
**Rules** scheduling forward pass with calendars (OrgService), critical path calc, baseline vs actual, task progress rollup, timesheet approval workflow, overallocation warning, earned value basics (PV/EV/AC/CPI/SPI) using budget + actual cost.
**Events out** project-mgmt.project.created|closed, task.completed|overdue, milestone.reached. **In** sales.order.confirmed (project type), hr-core.employee.*.
**Service contract** ProjectService: project(id), tasks(project), progress(project), evm(project,asOf).
**Permissions** project-mgmt.project|task|timesheet|risk.view|create|update|approve, project-mgmt.baseline.manage.
**Screens** project list/portfolio, Gantt with dependencies, kanban, my tasks, timesheet entry, risk register, status report print, EVM dashboard.
**AI** autofill (WBS from description, proposal), forecast delay risk, summarize status, next_action. **Datasets** tasks, timesheets, evm.
