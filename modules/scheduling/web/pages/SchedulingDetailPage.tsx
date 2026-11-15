import React from 'react';
import { PageHeader, Card } from '@erp/ui';

export const SchedulingDetailPage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="جزئیات زمان‌بندی تولید" />
    <Card>
      <p className="text-sm text-gray-600">نمایش جزئیات و تاریخچه زمان‌بندی تولید.</p>
    </Card>
  </div>
);
export default SchedulingDetailPage;
