import React from 'react';
import { PageHeader, Card } from '@erp/ui';

export const WorkOrderDetailPage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="جزئیات دستور کار تولید" />
    <Card>
      <p className="text-sm text-gray-600">نمایش جزئیات و تاریخچه دستور کار تولید.</p>
    </Card>
  </div>
);
export default WorkOrderDetailPage;
