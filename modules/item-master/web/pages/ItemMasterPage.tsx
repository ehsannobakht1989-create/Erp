import React, { useState } from 'react';
import { Card, DataTable, Input, Button } from '@erp/ui';
import { mockItems } from '../mocks/data';

export default function ItemMasterPage() {
  const [q, setQ] = useState('');
  const filtered = mockItems.filter((it) => it.name_fa.includes(q) || it.code.includes(q)).slice(0, 25);

  return (
    <div dir="rtl" className="p-6 space-y-4">
      <div className="flex items-center justify-between">
        <h1 className="text-2xl font-bold">اطلاعات پایه کالا</h1>
        <Button>کالای جدید</Button>
      </div>
      <Card>
        <div className="mb-3 max-w-sm">
          <Input placeholder="جستجو بر اساس کد یا نام..." value={q} onChange={(e) => setQ(e.target.value)} />
        </div>
        <DataTable
          columns={[
            { key: 'code', label: 'کد کالا' },
            { key: 'name_fa', label: 'نام فارسی' },
            { key: 'item_type', label: 'نوع' },
            { key: 'base_uom_id', label: 'واحد پایه' },
            { key: 'status', label: 'وضعیت' },
          ]}
          rows={filtered as unknown as Record<string, unknown>[]}
        />
      </Card>
    </div>
  );
}
