import React from 'react';
import { mockNotifications } from '../mocks/data';

export default function NotificationsPage() {
  return (
    <div dir="rtl" className="p-6 space-y-4">
      <h1 className="text-2xl font-bold">اعلان‌ها</h1>
      <div className="border rounded p-4">
        {mockNotifications.map((n) => (
          <div key={n.id} className="text-sm">{n.template} ({n.channel}) — {n.created_at}</div>
        ))}
      </div>
    </div>
  );
}
