import React from 'react';
import { PageHeader, Card } from '@erp/ui';

export const BudgetingDetailPage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="جزئیات بودجه‌بندی" />
    <Card>
      <p className="text-sm text-gray-600">نمایش جزئیات و تاریخچه بودجه‌بندی.</p>
    </Card>
  </div>
);
export default BudgetingDetailPage;
