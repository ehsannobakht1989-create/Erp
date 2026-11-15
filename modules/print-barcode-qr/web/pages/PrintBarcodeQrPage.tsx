import React from 'react';
import { mockTemplates } from '../mocks/data';

export default function PrintBarcodeQrPage() {
  return (
    <div dir="rtl" className="p-6 space-y-4">
      <h1 className="text-2xl font-bold">قالب‌های چاپ و بارکد</h1>
      <div className="border rounded p-4">
        {mockTemplates.map((t) => (
          <div key={t.id} className="text-sm">{t.name_fa} ({t.page_size})</div>
        ))}
      </div>
    </div>
  );
}
