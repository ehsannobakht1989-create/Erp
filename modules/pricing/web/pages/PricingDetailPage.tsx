import React from 'react';
import { PageHeader, Card } from '@erp/ui';

export const PricingDetailPage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="جزئیات قیمت‌گذاری" />
    <Card>
      <p className="text-sm text-gray-600">نمایش جزئیات و تاریخچه قیمت‌گذاری.</p>
    </Card>
  </div>
);
export default PricingDetailPage;
