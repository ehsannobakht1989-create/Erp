# canteen — Meal management (L3; requires hr-core; optional attendance, payroll, inventory, org-structure; low-code + kiosk)

**Entities** MealType{code,name,time_window,price,subsidy_rule}; Menu{date,meal_type,items[],capacity}; Reservation{employee|guest,menu,date,status[reserved|served|cancelled|no_show]}; Entitlement{rule(shift|unit|contract_type|attendance_present),meals_per_day}; Coupon{qr,employee,meal,valid_date,status}; ServingLog{reservation|coupon,time,device}; Charge{employee,period,amount (employee share)}; VendorInvoice (to ar-ap event); GuestMeal.
**Rules** reservation cutoff times; entitlement by attendance/shift optional (AttendanceService); QR/ID scan at kiosk marks served (idempotent); employee-share deductions exported as an aggregated event for payroll (no per-meal details beyond counts/amounts); capacity limits; waste tracking (reserved vs served); cost per meal report.
**Events out** canteen.period.charges_ready{employee,amount}, canteen.meal.served. **In** attendance.period.closed (optional), hr-core.employee.terminated.
**Service contract** CanteenService: entitled(employee,date), chargesFor(period).
**Permissions** canteen.menu.manage, canteen.reservation.create|view, canteen.serving.scan, canteen.report.view, canteen.charge.approve.
**Screens** employee reservation page (mobile), kiosk scan screen, menu planner, daily kitchen sheet (counts), charges review, cost dashboard.
**AI** forecast meal counts (python), anomaly (no-show patterns). **Datasets** meals.
