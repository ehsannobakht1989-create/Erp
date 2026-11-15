import React from 'react';
import { PageHeader, Card } from '@erp/ui';

export const TrainingLmsDetailPage: React.FC = () => (
  <div dir="rtl" className="space-y-4">
    <PageHeader title="جزئیات آموزش" />
    <Card>
      <p className="text-sm text-gray-600">نمایش جزئیات و تاریخچه آموزش.</p>
    </Card>
  </div>
);
export default TrainingLmsDetailPage;
