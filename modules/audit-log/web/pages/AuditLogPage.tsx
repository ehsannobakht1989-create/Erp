import React from 'react';
import { RecordHistory } from '../components/RecordHistory';

export default function AuditLogPage() {
  return (
    <div dir="rtl" className="p-6 space-y-4">
      <h1 className="text-2xl font-bold">گزارش ممیزی</h1>
      <RecordHistory entity="inventory.receipt" id={10} />
    </div>
  );
}
