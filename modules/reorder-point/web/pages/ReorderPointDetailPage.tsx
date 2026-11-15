import React from 'react';
import { PageHeader, Card } from '@erp/ui';

export const ReorderPointDetailPage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="جزئیات نقطه سفارش" />
    <Card>
      <p className="text-sm text-gray-600">نمایش جزئیات و تاریخچه نقطه سفارش.</p>
    </Card>
  </div>
);
export default ReorderPointDetailPage;
