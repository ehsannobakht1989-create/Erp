import React from 'react';
import { PageHeader, Card } from '@erp/ui';

export const ScheduleOptimizerDetailPage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="جزئیات بهینه‌ساز زمان‌بندی" />
    <Card>
      <p className="text-sm text-gray-600">نمایش جزئیات و تاریخچه بهینه‌ساز زمان‌بندی.</p>
    </Card>
  </div>
);
export default ScheduleOptimizerDetailPage;
