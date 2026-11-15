import React from 'react';
import { Card, DataTable } from '@erp/ui';
import { mockBoms } from '../mocks/data';

export default function BomPage() {
  return (
    <div dir="rtl" className="p-6 space-y-4">
      <h1 className="text-2xl font-bold">ساختار محصول (BOM)</h1>
      <Card>
        <DataTable columns={[{ key: 'code', label: 'کد' }, { key: 'revision', label: 'نسخه' }, { key: 'status', label: 'وضعیت' }]} rows={mockBoms as unknown as Record<string, unknown>[]} />
      </Card>
    </div>
  );
}
