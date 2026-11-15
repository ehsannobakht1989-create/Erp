import React from 'react';
import { PageHeader, Card } from '@erp/ui';

export const AccountingGlDetailPage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="جزئیات حسابداری کل" />
    <Card>
      <p className="text-sm text-gray-600">نمایش جزئیات و تاریخچه حسابداری کل.</p>
    </Card>
  </div>
);
export default AccountingGlDetailPage;
