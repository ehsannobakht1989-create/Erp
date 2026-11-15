import React from 'react';
import { PageHeader, Card } from '@erp/ui';

export const ShippingDistributionDetailPage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="جزئیات حمل و توزیع" />
    <Card>
      <p className="text-sm text-gray-600">نمایش جزئیات و تاریخچه حمل و توزیع.</p>
    </Card>
  </div>
);
export default ShippingDistributionDetailPage;
