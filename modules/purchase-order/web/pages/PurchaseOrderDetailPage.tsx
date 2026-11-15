import React from 'react';
import { PageHeader, Card } from '@erp/ui';

export const PurchaseOrderDetailPage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="جزئیات سفارش خرید" />
    <Card>
      <p className="text-sm text-gray-600">نمایش جزئیات و تاریخچه سفارش خرید.</p>
    </Card>
  </div>
);
export default PurchaseOrderDetailPage;
