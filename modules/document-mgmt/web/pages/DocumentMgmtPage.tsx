import React from 'react';
import { mockDocuments } from '../mocks/data';

export function Attachments({ entity, id }: { entity: string; id: string | number }) {
  return (
    <div dir="rtl" className="border rounded p-3 text-sm">
      <div className="font-semibold mb-1">پیوست‌ها ({entity} #{id})</div>
      {mockDocuments.map((d) => (
        <div key={d.id}>{d.filename} ({d.size_bytes} B)</div>
      ))}
    </div>
  );
}

export default function DocumentMgmtPage() {
  return (
    <div dir="rtl" className="p-6 space-y-4">
      <h1 className="text-2xl font-bold">مدیریت اسناد</h1>
      <Attachments entity="purchase-order.po" id={12} />
    </div>
  );
}
