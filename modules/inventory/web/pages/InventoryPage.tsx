import React from 'react';
import { Card, DataTable } from '@erp/ui';
import { mockBalances } from '../mocks/data';

export default function InventoryPage() {
  return (
    <div dir="rtl" className="p-6 space-y-4">
      <h1 className="text-2xl font-bold">انبار و موجودی</h1>
      <Card>
        <DataTable
          columns={[{ key: 'item_code', label: 'کد کالا' }, { key: 'warehouse', label: 'انبار' }, { key: 'qty', label: 'موجودی' }, { key: 'uom', label: 'واحد' }]}
          rows={mockBalances as unknown as Record<string, unknown>[]}
        />
      </Card>
    </div>
  );
}
