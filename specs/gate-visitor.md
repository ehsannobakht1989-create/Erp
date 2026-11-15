# gate-visitor — Gate, visitors & material passes (L3; optional hr-core, contractor-mgmt, weighbridge, inventory, hse)

**Entities** Visitor{name,company,id_ref(masked),phone,host,purpose,planned_date,badge_no,status[expected|checked_in|checked_out|denied],photo_ref}; VehicleEntry{plate,driver,type,purpose,in_at,out_at,weigh_ticket_ref}; MaterialPass(گیت‌پاس){direction[in|out],items[{desc|item_id,qty,uom}],doc_ref{delivery|po|return|scrap|loan},requester,approvals,valid_until,status[draft|approved|used|expired|cancelled]}; Blacklist{person|plate,reason}; ContractorEntry{contractor,workers[],permit_ref,doc_validity_ok}; MusterRecord (on-site list); GuardLog/rounds (optional).
**Rules** pre-registration by hosts; check-in with badge and host notification; blacklist block; contractor entry checks document/training validity via contractor-mgmt/hse/training services when enabled; material-out pass requires approval and matching document (delivery posted or approved return/loan), single-use QR scanned at gate; on-site list for emergency (visitors + vehicles + contractors + employees if attendance enabled); data retention/masking for visitor id numbers.
**Events out** gate-visitor.pass.entered|exited, gate-visitor.vehicle.entered|exited, gate-visitor.materialpass.used. **In** sales.delivery.posted, weighbridge.ticket.completed, hse.permit.issued.
**Service contract** GateService: onSite(), materialPass(ref), isBlacklisted(personOrPlate).
**Permissions** gate-visitor.visitor|vehicle|materialpass.view|create|approve|scan, gate-visitor.blacklist.manage.
**Screens** security desk (fast check-in/out, QR scan), pre-registration form, material pass form/approval/print, on-site/muster list, logs, reports.
**AI** anomaly (after-hours patterns). **Datasets** gate_logs.
**Iran** مجوز خروج کالا/گیت‌پاس, Persian print passes.
