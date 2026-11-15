import React from 'react';
import { Card, DataTable } from '@erp/ui';
import { mockUnits } from '../mocks/data';

export default function OrgStructurePage() {
  return (
    <div dir="rtl" className="p-6 space-y-4">
      <h1 className="text-2xl font-bold">ساختار سازمانی</h1>
      <Card>
        <DataTable
          columns={[{ key: 'code', label: 'کد' }, { key: 'name_fa', label: 'نام واحد' }, { key: 'unit_type', label: 'نوع' }]}
          rows={mockUnits as unknown as Record<string, unknown>[]}
        />
      </Card>
    </div>
  );
}
