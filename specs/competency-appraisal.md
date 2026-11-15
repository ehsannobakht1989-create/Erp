# competency-appraisal — Competencies & performance (L2; requires hr-core; optional training-lms, payroll)

**Entities** Competency{code,name,category,levels[{level,description}]}; JobCompetencyProfile{position|job_family,competency,required_level,weight}; Assessment{employee,cycle,assessor_roles[self|manager|peers|subordinates],ratings[],comments,status}; Cycle{name,from,to,type[competency|performance|360],status}; Goal/KPI{employee,cycle,title,weight,target,actual,score}; Calibration session; DevelopmentPlan{employee,actions[],due}; Succession{position,candidates[],readiness}; GapReport.
**Rules** weighted scoring; anonymity for peer feedback; calibration before finalizing; gaps computed vs required levels generate training needs (event); results visibility by role; payroll bonus inputs exposed ONLY as an approved aggregate event (no ratings text); immutable after finalize.
**Events out** competency-appraisal.cycle.closed, competency-appraisal.gap.identified, competency-appraisal.score.finalized (restricted). **In** hr-core.employee.*, training-lms.completion.recorded.
**Service contract** AppraisalService: gaps(employee), latestScore(employee) (restricted), competencyMatrix(unit).
**Permissions** competency-appraisal.model.manage, competency-appraisal.assessment.view-own|view-team|view-all|submit|calibrate.
**Screens** competency library, job profiles, assessment forms (self/manager/360), cycle manager, calibration grid, 9-box, development plans, gap heatmap.
**AI** summarize feedback themes (aggregate only), autofill (competency list for a job from description, proposal). `redact: *` for per-person content to external providers. **Datasets** competency_gaps (aggregated).
