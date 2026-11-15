import React from 'react';
import { PageHeader, Card } from '@erp/ui';

export const CostingDetailPage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="جزئیات بهای تمام‌شده" />
    <Card>
      <p className="text-sm text-gray-600">نمایش جزئیات و تاریخچه بهای تمام‌شده.</p>
    </Card>
  </div>
);
export default CostingDetailPage;
