import React from 'react';
import { PageHeader, Card } from '@erp/ui';

export const TaxMoadianDetailPage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="جزئیات مالیات و سامانه مودیان" />
    <Card>
      <p className="text-sm text-gray-600">نمایش جزئیات و تاریخچه مالیات و سامانه مودیان.</p>
    </Card>
  </div>
);
export default TaxMoadianDetailPage;
