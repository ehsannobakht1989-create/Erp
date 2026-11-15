import React from 'react';
import { PageHeader, Card } from '@erp/ui';

export const ArApDetailPage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="جزئیات حساب‌های دریافتنی و پرداختنی" />
    <Card>
      <p className="text-sm text-gray-600">نمایش جزئیات و تاریخچه حساب‌های دریافتنی و پرداختنی.</p>
    </Card>
  </div>
);
export default ArApDetailPage;
