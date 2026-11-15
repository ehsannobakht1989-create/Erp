import React from 'react';
import { mockAuditEntries } from '../mocks/data';

export function RecordHistory({ entity, id }: { entity: string; id: string | number }) {
  return (
    <div dir="rtl" className="border rounded p-3 text-sm">
      <div className="font-semibold mb-2">تاریخچه تغییرات ({entity} #{id})</div>
      {mockAuditEntries.map((e) => (
        <div key={e.id}>{e.action} — {e.created_at}</div>
      ))}
    </div>
  );
}
