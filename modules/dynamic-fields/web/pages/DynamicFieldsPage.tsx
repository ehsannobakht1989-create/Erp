import React from 'react';
import { mockDynamicFields } from '../mocks/data';

export function DynamicFieldsForm({ entity }: { entity: string }) {
  const fields = mockDynamicFields.filter((f) => f.entity === entity);
  return (
    <div dir="rtl" className="space-y-2">
      {fields.map((f) => (
        <div key={f.id}>
          <label className="block text-sm font-medium">{f.label_fa}</label>
          <input className="border rounded px-2 py-1 w-full" placeholder={f.field_key} />
        </div>
      ))}
    </div>
  );
}

export default function DynamicFieldsPage() {
  return (
    <div dir="rtl" className="p-6 space-y-4">
      <h1 className="text-2xl font-bold">فیلدهای پویا</h1>
      <DynamicFieldsForm entity="item-master.item" />
    </div>
  );
}
