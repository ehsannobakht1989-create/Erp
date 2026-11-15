import React from 'react';
import { Card, DataTable } from '@erp/ui';
import { mockEmployees } from '../mocks/data';

export default function HrCorePage() {
  return (
    <div dir="rtl" className="p-6 space-y-4">
      <h1 className="text-2xl font-bold">پرونده پرسنلی</h1>
      <Card>
        <DataTable columns={[{ key: 'personnel_no', label: 'کد پرسنلی' }, { key: 'full_name', label: 'نام و نام خانوادگی' }, { key: 'status', label: 'وضعیت' }]} rows={mockEmployees as unknown as Record<string, unknown>[]} />
      </Card>
    </div>
  );
}
