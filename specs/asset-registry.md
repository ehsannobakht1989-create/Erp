# asset-registry — Equipment & technical assets (L2; requires org-structure)

**Entities** Asset{code,name,class_id,parent_id,serial,manufacturer,model,location_id,unit_id,cost_center_id,commissioned_at,status[planned|active|standby|down|decommissioned],criticality[A|B|C],qr_code,warranty_until}; AssetClass(fields via dynamic-fields); Meter{asset_id,type[hours|cycles|km|kwh],unit,current_value}; MeterReading; SpareBom{asset_id,item_id,qty,critical}; AssetDocument via document-mgmt; AssetEvent history.
**Rules** hierarchy no cycles; status transitions logged; meter readings monotonic (allow reset with reason); decommission needs approval; warranty alerts via notifications.
**Events out** asset-registry.asset.created|status_changed|meter.updated|decommissioned. **In** fixed-assets.asset.capitalized (link, optional).
**Service contract** AssetService: find(id), tree(root), byQr(code), currentMeter(asset,type), addReading(dto), spareParts(asset), setStatus(id,status,reason), lookup(q).
**Permissions** asset-registry.asset.view|create|update|delete|status, asset-registry.meter.record, asset-registry.class.manage.
**Screens** asset tree, asset card (tabs: General, Specs, Meters, Spares, Documents, History, linked WOs via cmms widget), QR label print, import, dashboards (by status/criticality).
**AI** assistant (find similar equipment/spares), autofill (specs from nameplate text), anomaly (meter jumps).
**Iran** support bilingual names, supplier/manufacturer free-text.
