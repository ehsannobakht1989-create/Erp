import React from 'react';
import { Card, DataTable } from '@erp/ui';
import { mockJournals } from '../mocks/data';

export default function AccountingGlPage() {
  return (
    <div dir="rtl" className="p-6 space-y-4">
      <h1 className="text-2xl font-bold">حسابداری کل</h1>
      <Card>
        <DataTable columns={[{ key: 'journal_no', label: 'شماره سند' }, { key: 'journal_date', label: 'تاریخ' }, { key: 'status', label: 'وضعیت' }]} rows={mockJournals as unknown as Record<string, unknown>[]} />
      </Card>
    </div>
  );
}
