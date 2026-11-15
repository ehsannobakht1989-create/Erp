import React from 'react';
import { PageHeader, Card } from '@erp/ui';

export const WorkflowEngineDetailPage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="جزئیات موتور گردش کار" />
    <Card>
      <p className="text-sm text-gray-600">نمایش جزئیات و تاریخچه موتور گردش کار.</p>
    </Card>
  </div>
);
export default WorkflowEngineDetailPage;
