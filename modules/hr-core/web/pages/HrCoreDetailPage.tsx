import React from 'react';
import { PageHeader, Card } from '@erp/ui';

export const HrCoreDetailPage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="جزئیات منابع انسانی" />
    <Card>
      <p className="text-sm text-gray-600">نمایش جزئیات و تاریخچه منابع انسانی.</p>
    </Card>
  </div>
);
export default HrCoreDetailPage;
