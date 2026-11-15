import React from 'react';
import { PageHeader, Card } from '@erp/ui';

export const PurchaseRequestDetailPage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="جزئیات درخواست خرید" />
    <Card>
      <p className="text-sm text-gray-600">نمایش جزئیات و تاریخچه درخواست خرید.</p>
    </Card>
  </div>
);
export default PurchaseRequestDetailPage;
