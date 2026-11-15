import React from 'react';
import { mockForms } from '../mocks/data';

export default function FormBuilderPage() {
  return (
    <div dir="rtl" className="p-6 space-y-4">
      <h1 className="text-2xl font-bold">فرم‌ساز</h1>
      <div className="border rounded p-4">
        {mockForms.map((f) => (
          <div key={f.id} className="text-sm">{f.title_fa} ({f.slug} v{f.version})</div>
        ))}
      </div>
    </div>
  );
}
