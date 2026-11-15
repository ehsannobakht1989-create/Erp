import React from 'react';
import { PageHeader, Card } from '@erp/ui';

export const InventoryDetailPage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="جزئیات انبار" />
    <Card>
      <p className="text-sm text-gray-600">نمایش جزئیات و تاریخچه انبار.</p>
    </Card>
  </div>
);
export default InventoryDetailPage;
