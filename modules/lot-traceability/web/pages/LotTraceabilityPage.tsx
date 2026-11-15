import React from 'react';
import { Card, DataTable } from '@erp/ui';
import { mockRecords } from '../mocks/data';

export default function LotTraceabilityPage() {
  return (
    <div dir="rtl" className="p-6 space-y-4">
      <h1 className="text-2xl font-bold">ردیابی بچ و لات</h1>
      <Card>
        <DataTable columns={[{ key: 'code', label: 'کد' }, { key: 'status', label: 'وضعیت' }]} rows={mockRecords as unknown as Record<string, unknown>[]} />
      </Card>
    </div>
  );
}
