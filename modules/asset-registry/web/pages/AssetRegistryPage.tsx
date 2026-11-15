import React from 'react';
import { Card, DataTable } from '@erp/ui';
import { mockAssets } from '../mocks/data';

export default function AssetRegistryPage() {
  return (
    <div dir="rtl" className="p-6 space-y-4">
      <h1 className="text-2xl font-bold">تجهیزات و دارایی‌ها</h1>
      <Card>
        <DataTable columns={[{ key: 'code', label: 'کد' }, { key: 'name_fa', label: 'نام تجهیز' }, { key: 'criticality', label: 'حساسیت' }, { key: 'status', label: 'وضعیت' }]} rows={mockAssets as unknown as Record<string, unknown>[]} />
      </Card>
    </div>
  );
}
