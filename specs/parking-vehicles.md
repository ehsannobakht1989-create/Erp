# parking-vehicles — Fleet & parking (L3; optional hr-core, cmms, gate-visitor, asset-registry)

**Entities** Vehicle{plate,type,make,model,year,vin,owner_type[company|employee|contractor|rental],odometer,status,assigned_driver,cost_center}; VehicleDocument{type[insurance|inspection|tax|license],number,expiry}; Trip{vehicle,driver,from,to,purpose,start_odo,end_odo,start,end,requester,approval}; FuelLog{vehicle,date,liters,amount,odometer,station}; VehicleRequest{requester,purpose,from,to,status}; ParkingSlot{zone,code,type,assigned_to}; ParkingPermit{vehicle|employee,slot,valid_from/to}; MaintenanceLink (cmms WO ref or simple service log).
**Rules** document expiry alerts; trip approval workflow; odometer monotonic; fuel consumption per 100 km analytics; request→dispatch assignment with availability by calendar; permits conflict checks.
**Events out** parking-vehicles.document.expiring, parking-vehicles.trip.completed. **In** gate-visitor.vehicle.entered|exited, cmms.workorder.completed.
**Service contract** FleetService: availability(range), documentsExpiring(days), costPerKm(vehicle,range).
**Permissions** parking-vehicles.vehicle|trip|fuel|request|parking.view|create|update|approve.
**Screens** fleet register, trip log/requests (mobile), fuel entries, expiring documents, parking map as grid, cost reports.
**AI** anomaly (fuel theft patterns), summarize costs. **Datasets** trips, fuel.
**Iran** بیمه شخص ثالث/معاینه فنی/عوارض, plate format.
