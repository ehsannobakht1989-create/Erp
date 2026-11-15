import React from 'react';
import { PageHeader, Card } from '@erp/ui';

export const DemandForecastDetailPage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="جزئیات پیش‌بینی تقاضا" />
    <Card>
      <p className="text-sm text-gray-600">نمایش جزئیات و تاریخچه پیش‌بینی تقاضا.</p>
    </Card>
  </div>
);
export default DemandForecastDetailPage;
