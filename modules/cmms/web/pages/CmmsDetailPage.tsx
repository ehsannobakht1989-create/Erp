import React from 'react';
import { PageHeader, Card } from '@erp/ui';

export const CmmsDetailPage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="جزئیات نگهداری و تعمیرات" />
    <Card>
      <p className="text-sm text-gray-600">نمایش جزئیات و تاریخچه نگهداری و تعمیرات.</p>
    </Card>
  </div>
);
export default CmmsDetailPage;
