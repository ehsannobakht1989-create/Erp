import React from 'react';
import { PageHeader, Card } from '@erp/ui';

export const PayrollDetailPage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="جزئیات حقوق و دستمزد" />
    <Card>
      <p className="text-sm text-gray-600">نمایش جزئیات و تاریخچه حقوق و دستمزد.</p>
    </Card>
  </div>
);
export default PayrollDetailPage;
