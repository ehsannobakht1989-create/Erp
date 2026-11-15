import React from 'react';
import { PageHeader, Card } from '@erp/ui';

export const HseDetailPage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="جزئیات ایمنی و بهداشت" />
    <Card>
      <p className="text-sm text-gray-600">نمایش جزئیات و تاریخچه ایمنی و بهداشت.</p>
    </Card>
  </div>
);
export default HseDetailPage;
