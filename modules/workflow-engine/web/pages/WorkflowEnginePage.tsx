import React from 'react';
import { mockTasks } from '../mocks/data';

export default function WorkflowEnginePage() {
  return (
    <div dir="rtl" className="p-6 space-y-4">
      <h1 className="text-2xl font-bold">کارتابل و گردش کار</h1>
      <div className="border rounded p-4">
        {mockTasks.map((t) => (
          <div key={t.id} className="text-sm">{t.workflow_code} #{t.entity_id} — {t.current_state}</div>
        ))}
      </div>
    </div>
  );
}
