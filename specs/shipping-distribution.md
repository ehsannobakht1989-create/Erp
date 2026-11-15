# shipping-distribution — Transport & delivery (L3; requires sales; optional inventory, weighbridge, gate-visitor, parking-vehicles, party-master, python)

**Entities** Carrier{party_ref,type[own_fleet|third_party],rates}; Vehicle (own: ref to parking-vehicles optional){plate,type,capacity_kg,capacity_m3}; Driver; Shipment{number,deliveries[],carrier,vehicle,driver,route,planned_dispatch,status[planned|loading|dispatched|in_transit|delivered|partially_delivered|returned|cancelled],freight_cost,waybill_no}; ShipmentStop{delivery_ref,address,eta,actual_arrival,proof{signature,photo,receiver}}; Waybill(بارنامه) fields; CarrierInvoice (to ar-ap via event); RouteTemplate; ReturnPickup.
**Rules** load building respects weight/volume limits; shipment locks deliveries (sales.delivery) status transitions via events; freight cost allocation to deliveries/customers (setting); proof of delivery capture mobile; delivery performance (OTIF) metrics; optional route optimization via python (OR-Tools) as proposal.
**Events out** shipping-distribution.shipment.dispatched|delivered|failed, shipping-distribution.freight.cost_posted. **In** sales.delivery.requested|posted, weighbridge.ticket.completed, gate-visitor.pass.exited.
**Service contract** ShippingService: shipmentFor(deliveryId), eta(deliveryId), carrierCost(range), otif(range).
**Permissions** shipping-distribution.shipment|carrier|waybill.view|create|update|dispatch|close.
**Screens** shipment planner (drag deliveries into vehicles), dispatch board/map placeholder (no external map dependency; coordinates optional), driver mobile view with POD, waybill print, freight cost report, OTIF dashboard.
**AI** next_action (load consolidation suggestions), anomaly (cost per ton outliers), forecast transport demand. **Datasets** shipments, freight_costs.
**Iran** بارنامه/صورت‌وضعیت حمل fields as data, truck types, ton-based pricing.
