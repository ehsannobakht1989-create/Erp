import React from 'react';
import { PageHeader, Card } from '@erp/ui';

export const DynamicFieldsDetailPage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="جزئیات فیلدهای پویا" />
    <Card>
      <p className="text-sm text-gray-600">نمایش جزئیات و تاریخچه فیلدهای پویا.</p>
    </Card>
  </div>
);
export default DynamicFieldsDetailPage;
