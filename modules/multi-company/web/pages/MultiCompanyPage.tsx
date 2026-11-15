import React from 'react';
import { mockCompanies } from '../mocks/data';

export default function MultiCompanyPage() {
  return (
    <div dir="rtl" className="p-6 space-y-4">
      <h1 className="text-2xl font-bold">شرکت‌ها و تنظیمات</h1>
      <div className="border rounded p-4">
        {mockCompanies.map((c) => (
          <div key={c.id} className="text-sm">{c.name} ({c.code}) — {c.base_currency}</div>
        ))}
      </div>
    </div>
  );
}
