import React from 'react';
import { PageHeader, Card } from '@erp/ui';

export const SalesDetailPage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="جزئیات فروش" />
    <Card>
      <p className="text-sm text-gray-600">نمایش جزئیات و تاریخچه فروش.</p>
    </Card>
  </div>
);
export default SalesDetailPage;
