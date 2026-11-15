import React from 'react';
import { PageHeader, Card } from '@erp/ui';

export const CrmDetailPage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="جزئیات مدیریت ارتباط با مشتری" />
    <Card>
      <p className="text-sm text-gray-600">نمایش جزئیات و تاریخچه مدیریت ارتباط با مشتری.</p>
    </Card>
  </div>
);
export default CrmDetailPage;
