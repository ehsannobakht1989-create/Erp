# waste-environment — Waste & environmental compliance (L5; optional hse, scrap-byproduct, party-master, document-mgmt)

**Entities** WasteStream{code,name,category[hazardous|non_hazardous|recyclable|liquid|gas],handling,storage_rules,legal_code}; WasteRecord{stream,source_unit,qty,uom,date,container,storage_location,status[stored|manifested|disposed]}; Manifest{contractor_id,vehicle,date,records[],documents,status}; DisposalContractor{party_ref,license_no,expiry,categories}; EnvMeasurement{point,parameter,value,unit,limit,date,lab_ref}; Permit/License{type,number,authority,expiry,conditions}; ComplianceTask{title,due,owner,status}; EnvIncident (links hse).
**Rules** contractor license validity & category check at manifest; storage limits/time alerts; measurement vs limit evaluation with exceedance alerts; compliance calendar with recurring tasks; yearly waste/emission summary reports; link scrap-byproduct dispositions as waste records.
**Events out** waste-environment.exceedance.raised, waste-environment.license.expiring. **In** scrap-byproduct.scrap.disposed, hse.incident.reported(environmental).
**Service contract** WasteService: stored(stream), contractorsValid(category), exceedances(range).
**Permissions** waste-environment.waste|manifest|measurement|license|task.view|manage.
**Screens** waste register, manifests with print, measurement entry + trend vs limits, licenses/permits board, compliance calendar, annual report.
**AI** anomaly (measurement drift), summarize compliance status. **Datasets** waste_records, env_measurements.
**Iran** Persian environmental form labels, authority names as data.
