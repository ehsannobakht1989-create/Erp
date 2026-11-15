# weighbridge — Truck scale (L3; requires party-master; optional purchase-order, sales, inventory, scrap-byproduct, gate-visitor, shipping-distribution)

**Entities** Scale{name,device_agent_id,capacity,unit,calibration_ref}; WeighTicket{number,type[inbound|outbound|transfer|scrap|other],vehicle_plate,driver,party_id,item_id,doc_ref{po|delivery|scrap_sale},first_weight{value,at,by},second_weight{...},tare,gross,net,declared_net,tolerance_status,status[open|first_done|completed|voided],photos[refs]}; VehicleTare{plate,tare,valid_until}; Override{ticket,reason,approver}.
**Rules** one-pass (stored tare) or two-pass modes; stable-weight capture from device agent; net vs document qty tolerance check (setting) with warning/block; manual weight entry only with permission + reason + approval + audit flag; void with reason; tickets immutable after complete (corrections via new ticket); receipt/delivery documents updated through events not direct writes; daily reconciliation report; duplicate-plate-in-open-ticket guard.
**Local agent:** `modules/weighbridge/agent/` a single-file Python script (reads serial/TCP scale, posts stable weights to `/api/weighbridge/scale-readings` with API key); documented in README; optional, not required for manual mode.
**Events out** weighbridge.ticket.completed{net,doc_ref}, weighbridge.ticket.voided. **In** purchase-order.po.approved (expected deliveries), sales.delivery.requested, gate-visitor.pass.entered.
**Service contract** WeighbridgeService: ticket(id), netFor(docRef), pendingTickets(plate?).
**Permissions** weighbridge.ticket.view|create|complete|void|override, weighbridge.scale.manage, weighbridge.tare.manage.
**Screens** weighing console (big live weight, plate search, doc picker, capture first/second), ticket print (thermal + A5), open tickets, daily report, tare register, scale settings.
**AI** anomaly (tare drift, repeated manual entries, net vs doc mismatch). **Datasets** weigh_tickets.
**Iran** تیکت باسکول/توزین فارسی, truck plate format (Iranian plate parts) input helper, ton/kg units.
