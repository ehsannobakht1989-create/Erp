import React from 'react';
import { mockWebhooks } from '../mocks/data';

export default function ApiWebhooksPage() {
  return (
    <div dir="rtl" className="p-6 space-y-4">
      <h1 className="text-2xl font-bold">کلیدهای API و وب‌هوک</h1>
      <div className="border rounded p-4">
        {mockWebhooks.map((w) => (
          <div key={w.id} className="text-sm">{w.event_pattern} → {w.target_url}</div>
        ))}
      </div>
    </div>
  );
}
