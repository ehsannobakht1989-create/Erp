import React from 'react';
import { Card, DataTable, Button } from '@erp/ui';
import { mockParties } from '../mocks/data';

export default function PartyMasterPage() {
  return (
    <div dir="rtl" className="p-6 space-y-4">
      <div className="flex items-center justify-between">
        <h1 className="text-2xl font-bold">اشخاص و طرف‌حساب‌ها</h1>
        <Button>شخص جدید</Button>
      </div>
      <Card>
        <DataTable
          columns={[
            { key: 'code', label: 'کد' },
            { key: 'name_fa', label: 'نام' },
            { key: 'party_type', label: 'نوع' },
            { key: 'status', label: 'وضعیت' },
          ]}
          rows={mockParties as unknown as Record<string, unknown>[]}
        />
      </Card>
    </div>
  );
}
