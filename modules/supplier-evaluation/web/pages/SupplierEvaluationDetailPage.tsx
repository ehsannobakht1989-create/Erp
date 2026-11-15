import React from 'react';
import { PageHeader, Card } from '@erp/ui';

export const SupplierEvaluationDetailPage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="جزئیات ارزیابی تامین‌کنندگان" />
    <Card>
      <p className="text-sm text-gray-600">نمایش جزئیات و تاریخچه ارزیابی تامین‌کنندگان.</p>
    </Card>
  </div>
);
export default SupplierEvaluationDetailPage;
