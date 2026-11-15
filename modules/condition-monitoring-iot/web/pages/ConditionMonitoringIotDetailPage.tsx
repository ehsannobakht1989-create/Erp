import React from 'react';
import { PageHeader, Card } from '@erp/ui';

export const ConditionMonitoringIotDetailPage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="جزئیات پایش وضعیت و IoT" />
    <Card>
      <p className="text-sm text-gray-600">نمایش جزئیات و تاریخچه پایش وضعیت و IoT.</p>
    </Card>
  </div>
);
export default ConditionMonitoringIotDetailPage;
