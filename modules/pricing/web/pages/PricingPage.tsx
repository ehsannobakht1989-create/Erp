import React from 'react';
import { Card, DataTable } from '@erp/ui';
import { mockPriceLists } from '../mocks/data';

export default function PricingPage() {
  return (
    <div dir="rtl" className="p-6 space-y-4">
      <h1 className="text-2xl font-bold">لیست‌های قیمت</h1>
      <Card>
        <DataTable columns={[{ key: 'code', label: 'کد' }, { key: 'list_type', label: 'نوع' }, { key: 'currency', label: 'ارز' }]} rows={mockPriceLists as unknown as Record<string, unknown>[]} />
      </Card>
    </div>
  );
}
